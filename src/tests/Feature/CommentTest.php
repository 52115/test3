<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みのユーザーはコメントを送信できる
     */
    public function test_authenticated_user_can_post_comment(): void
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

        $initialCommentCount = $item->comments()->count();

        $response = $this->actingAs($user)->post("/comment/{$item->id}", [
            'content' => 'テストコメント',
        ]);

        $response->assertRedirect("/item/{$item->id}");
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);

        $finalCommentCount = $item->comments()->count();
        $this->assertEquals($initialCommentCount + 1, $finalCommentCount);
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_unauthenticated_user_cannot_post_comment(): void
    {
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

        $response = $this->post("/comment/{$item->id}", [
            'content' => 'テストコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_validation_error_when_content_is_empty(): void
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

        $response = $this->actingAs($user)->post("/comment/{$item->id}", []);

        $response->assertSessionHasErrors(['content']);
    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_validation_error_when_content_exceeds_255_characters(): void
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

        $longContent = str_repeat('a', 256);

        $response = $this->actingAs($user)->post("/comment/{$item->id}", [
            'content' => $longContent,
        ]);

        $response->assertSessionHasErrors(['content']);
    }
}

