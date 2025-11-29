<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録することができる
     */
    public function test_can_add_favorite(): void
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

        $response = $this->actingAs($user)->post("/favorite/{$item->id}");

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $favoriteCount = $item->favorites()->count();
        $this->assertEquals(1, $favoriteCount);
    }

    /**
     * 追加済みのアイコンは色が変化する
     */
    public function test_favorite_icon_color_changes_when_favorited(): void
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

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->assertStatus(200);
        $isFavorite = $response->viewData('isFavorite');
        $this->assertTrue($isFavorite);
    }

    /**
     * 再度いいねアイコンを押下することによって、いいねを解除することができる
     */
    public function test_can_remove_favorite(): void
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

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->post("/favorite/{$item->id}");

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $favoriteCount = $item->favorites()->count();
        $this->assertEquals(0, $favoriteCount);
    }
}

