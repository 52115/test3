<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 変更項目が初期値として過去設定されていること
     */
    public function test_profile_edit_shows_initial_values(): void
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => '/storage/profiles/test.jpg',
            'postal_code' => '123-4567',
            'address' => 'テスト住所',
            'building_name' => 'テストビル',
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー', false);
        $response->assertSee('123-4567', false);
        $response->assertSee('テスト住所', false);
        $response->assertSee('テストビル', false);
    }
}

