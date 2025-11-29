<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 必要な情報が取得できる
     */
    public function test_profile_shows_all_required_information(): void
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => '/storage/profiles/test.jpg',
        ]);
        $category = Category::create(['name' => 'テストカテゴリ']);

        // 出品した商品
        $soldItem = Item::create([
            'user_id' => $user->id,
            'name' => '出品商品',
            'description' => '説明',
            'price' => 5000,
            'condition' => '良好',
            'image_url' => '/test/image.jpg',
        ]);
        $soldItem->categories()->attach($category->id);

        // 購入した商品
        $seller = User::factory()->create();
        $purchasedItem = Item::create([
            'user_id' => $seller->id,
            'name' => '購入商品',
            'description' => '説明',
            'price' => 10000,
            'condition' => '良好',
            'image_url' => '/test/image2.jpg',
            'buyer_id' => $user->id,
        ]);
        $purchasedItem->categories()->attach($category->id);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'payment_method' => 'カード支払い',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー', false);
        $response->assertViewHas('user');
        $response->assertViewHas('soldItems');
        $response->assertViewHas('purchasedItems');

        $soldItems = $response->viewData('soldItems');
        $purchasedItems = $response->viewData('purchasedItems');

        $this->assertCount(1, $soldItems);
        $this->assertCount(1, $purchasedItems);
        $this->assertEquals('出品商品', $soldItems->first()->name);
        $this->assertEquals('購入商品', $purchasedItems->first()->name);
    }
}

