<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねした商品だけが表示される
     */
    public function test_only_favorited_items_are_displayed(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::create(['name' => 'テストカテゴリ']);

        $item1 = Item::create([
            'user_id' => $otherUser->id,
            'name' => '商品1',
            'description' => '説明1',
            'price' => 1000,
            'condition' => '良好',
            'image_url' => '/test/image1.jpg',
        ]);
        $item1->categories()->attach($category->id);

        $item2 = Item::create([
            'user_id' => $otherUser->id,
            'name' => '商品2',
            'description' => '説明2',
            'price' => 2000,
            'condition' => '良好',
            'image_url' => '/test/image2.jpg',
        ]);
        $item2->categories()->attach($category->id);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);

        $response = $this->actingAs($user)->get('/mylist');

        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertCount(1, $items);
        $this->assertEquals('商品1', $items->first()->name);
    }

    /**
     * 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_show_sold_label_in_mylist(): void
    {
        $user = User::factory()->create();
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

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/mylist');

        $response->assertStatus(200);
        $response->assertSee('Sold', false);
    }

    /**
     * 未認証の場合は何も表示されない
     */
    public function test_unauthenticated_user_sees_no_items(): void
    {
        $response = $this->get('/mylist');

        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertCount(0, $items);
    }
}

