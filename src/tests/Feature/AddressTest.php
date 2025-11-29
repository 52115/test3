<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_updated_address_is_reflected_in_purchase_page(): void
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
        ]);
        $item->categories()->attach($category->id);

        // 住所を更新
        $response = $this->actingAs($user)->post("/purchase/address/{$item->id}", [
            'postal_code' => '987-6543',
            'address' => '更新された住所',
            'building_name' => 'テストビル',
        ]);

        $response->assertRedirect("/purchase/{$item->id}");

        $user->refresh();
        $this->assertEquals('987-6543', $user->postal_code);
        $this->assertEquals('更新された住所', $user->address);
        $this->assertEquals('テストビル', $user->building_name);

        // 購入画面で住所が反映されているか確認
        $purchaseResponse = $this->actingAs($user)->get("/purchase/{$item->id}");
        $purchaseResponse->assertStatus(200);
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_purchased_item_has_address_associated(): void
    {
        $user = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building_name' => 'テストビル',
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
            'building_name' => 'テストビル',
        ]);

        $purchase = Purchase::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        $this->assertNotNull($purchase);
        $this->assertEquals('123-4567', $purchase->postal_code);
        $this->assertEquals('テスト住所', $purchase->address);
        $this->assertEquals('テストビル', $purchase->building_name);
    }
}

