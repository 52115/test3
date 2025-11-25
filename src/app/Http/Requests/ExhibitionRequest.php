<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'description' => ['required', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'condition' => ['required', 'string'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:categories,id'],
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください',
            'name.max' => '商品名は255文字以内で入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は255文字以内で入力してください',
            'brand_name.max' => 'ブランド名は255文字以内で入力してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格は数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
            'condition.required' => '商品の状態を選択してください',
            'categories.required' => 'カテゴリーを選択してください',
            'categories.array' => 'カテゴリーを選択してください',
            'categories.min' => 'カテゴリーを1つ以上選択してください',
            'categories.*.exists' => '選択されたカテゴリーが無効です',
            'image.required' => '商品画像を選択してください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '画像はjpeg、png形式で選択してください',
            'image.max' => '画像サイズは2MB以下で選択してください',
        ];
    }
}

