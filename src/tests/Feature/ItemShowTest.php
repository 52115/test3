<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 必要な情報が表示される
     */
    public function test_item_detail_shows_all_required_information(): void
    {
        $user = User::factory()->create();
        $category1 = Category::create(['name' => 'カテゴリ1']);
        $category2 = Category::create(['name' => 'カテゴリ2']);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'description' => '商品説明',
            'brand_name' => 'テストブランド',
            'price' => 10000,
            'condition' => '良好',
            'image_url' => '/test/image.jpg',
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);

        $commentUser = User::factory()->create();
        Comment::create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'content' => 'コメント内容',
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('テスト商品', false);
        $response->assertSee('テストブランド', false);
        $response->assertSee('10,000', false); // 価格はフォーマットされる
        $response->assertSee('商品説明', false);
        $response->assertSee('カテゴリ1', false);
        $response->assertSee('カテゴリ2', false);
        $response->assertSee('良好', false);
    }

    /**
     * 複数選択されたカテゴリが表示されているか
     */
    public function test_multiple_categories_are_displayed(): void
    {
        $user = User::factory()->create();
        $category1 = Category::create(['name' => 'カテゴリ1']);
        $category2 = Category::create(['name' => 'カテゴリ2']);
        $category3 = Category::create(['name' => 'カテゴリ3']);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'description' => '商品説明',
            'price' => 10000,
            'condition' => '良好',
            'image_url' => '/test/image.jpg',
        ]);
        $item->categories()->attach([$category1->id, $category2->id, $category3->id]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('カテゴリ1', false);
        $response->assertSee('カテゴリ2', false);
        $response->assertSee('カテゴリ3', false);
    }
}

