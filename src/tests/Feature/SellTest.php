<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * 商品出品画面にて必要な情報が保存できること
     */
    public function test_can_save_item_with_all_required_fields(): void
    {
        $user = User::factory()->create();
        $category1 = Category::create(['name' => 'カテゴリ1']);
        $category2 = Category::create(['name' => 'カテゴリ2']);

        $image = UploadedFile::fake()->image('item.jpg');

        $response = $this->actingAs($user)->post('/sell', [
            'name' => 'テスト商品',
            'description' => '商品の説明',
            'brand_name' => 'テストブランド',
            'price' => 10000,
            'condition' => '良好',
            'categories' => [$category1->id, $category2->id],
            'image' => $image,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'description' => '商品の説明',
            'brand_name' => 'テストブランド',
            'price' => 10000,
            'condition' => '良好',
        ]);

        $item = Item::where('name', 'テスト商品')->first();
        $this->assertNotNull($item);
        $this->assertCount(2, $item->categories);
        $this->assertTrue($item->categories->contains($category1));
        $this->assertTrue($item->categories->contains($category2));
    }
}

