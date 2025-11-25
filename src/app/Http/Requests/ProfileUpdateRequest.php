<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            'building_name' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は255文字以内で入力してください',
            'postal_code.max' => '郵便番号は10文字以内で入力してください',
            'address.max' => '住所は255文字以内で入力してください',
            'building_name.max' => '建物名は255文字以内で入力してください',
            'profile_image.image' => '画像ファイルを選択してください',
            'profile_image.mimes' => '画像はjpeg、png、jpg、gif形式で選択してください',
            'profile_image.max' => '画像サイズは2MB以下で選択してください',
        ];
    }
}
