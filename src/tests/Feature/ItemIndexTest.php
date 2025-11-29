<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 全商品を取得できる
     */
    public function test_can_get_all_items(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $item1 = Item::create([
            'user_id' => $user->id,
            'name' => '商品1',
            'description' => '説明1',
            'price' => 1000,
            'condition' => '良好',
            'image_url' => '/test/image1.jpg',
        ]);
        $item1->categories()->attach($category->id);

        $item2 = Item::create([
            'user_id' => $user->id,
            'name' => '商品2',
            'description' => '説明2',
            'price' => 2000,
            'condition' => '良好',
            'image_url' => '/test/image2.jpg',
        ]);
        $item2->categories()->attach($category->id);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('items');
        $items = $response->viewData('items');
        $this->assertCount(2, $items);
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_show_sold_label(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => '商品1',
            'description' => '説明1',
            'price' => 1000,
            'condition' => '良好',
            'image_url' => '/test/image1.jpg',
            'buyer_id' => $buyer->id,
        ]);
        $item->categories()->attach($category->id);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sold', false);
    }

    /**
     * 自分が出品した商品は表示されない
     */
    public function test_own_items_are_not_displayed(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $ownItem = Item::create([
            'user_id' => $user->id,
            'name' => '自分の商品',
            'description' => '説明',
            'price' => 1000,
            'condition' => '良好',
            'image_url' => '/test/image1.jpg',
        ]);
        $ownItem->categories()->attach($category->id);

        $otherItem = Item::create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
            'description' => '説明',
            'price' => 2000,
            'condition' => '良好',
            'image_url' => '/test/image2.jpg',
        ]);
        $otherItem->categories()->attach($category->id);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertCount(1, $items);
        $this->assertEquals('他人の商品', $items->first()->name);
    }
}

