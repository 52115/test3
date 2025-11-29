<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Stripeの設定を無効化（テスト環境では不要）
        Config::set('services.stripe.secret', null);
    }

    /**
     * 「購入する」ボタンを押下すると購入が完了する
     */
    public function test_can_complete_purchase(): void
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
        ]);
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'description' => '商品説明',
            'price' => 10000,
            'condition' => '良好',
            'image_url' => '/test/image.jpg',
        ]);
        $item->categories()->attach($category->id);

        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'カード支払い',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'カード支払い',
        ]);

        $item->refresh();
        $this->assertEquals($user->id, $item->buyer_id);
    }

    /**
     * 購入した商品は商品一覧画面にて「sold」と表示される
     */
    public function test_purchased_item_shows_sold_in_item_list(): void
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'description' => '商品説明',
            'price' => 10000,
            'condition' => '良好',
            'image_url' => '/test/image.jpg',
            'buyer_id' => $user->id,
        ]);
        $item->categories()->attach($category->id);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold', false);
    }

    /**
     * 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_purchased_item_appears_in_profile_purchased_items(): void
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'description' => '商品説明',
            'price' => 10000,
            'condition' => '良好',
            'image_url' => '/test/image.jpg',
            'buyer_id' => $user->id,
        ]);
        $item->categories()->attach($category->id);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'カード支払い',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=purchase');

        $response->assertStatus(200);
        $purchasedItems = $response->viewData('purchasedItems');
        $this->assertCount(1, $purchasedItems);
        $this->assertEquals('テスト商品', $purchasedItems->first()->name);
    }
}

