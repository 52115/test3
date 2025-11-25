@extends('layouts.app')

@section('title', '商品の出品')

@section('content')
<div class="form-container">
    <h1>商品の出品</h1>
    
    <form method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label>商品画像</label>
            <div style="border: 2px dashed #ddd; padding: 2rem; text-align: center; margin-bottom: 1rem;">
                <input type="file" name="image" id="image" accept="image/*" required style="display: none;" onchange="previewImage(this)">
                <label for="image" style="cursor: pointer; color: #e74c3c; border: 1px solid #e74c3c; padding: 0.5rem 1rem; border-radius: 4px; display: inline-block;">画像を選択する</label>
                <div id="image-preview" style="margin-top: 1rem;"></div>
            </div>
            @error('image')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label>商品の詳細</label>
            <div style="margin-bottom: 1rem;">
                <label>カテゴリー</label>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                    @foreach($categories as $category)
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" style="margin-right: 0.5rem;">
                            <span style="background: #f0f0f0; padding: 0.5rem 1rem; border-radius: 20px;">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('categories')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div>
                <label for="condition">商品の状態</label>
                <select name="condition" id="condition" required>
                    <option value="">選択してください</option>
                    <option value="良好">良好</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="状態が悪い">状態が悪い</option>
                </select>
                @error('condition')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-group">
            <label>商品名と説明</label>
            <div style="margin-bottom: 1rem;">
                <label for="name">商品名</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label for="brand_name">ブランド名</label>
                <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">
                @error('brand_name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div>
                <label for="description">商品の説明</label>
                <textarea name="description" id="description" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-group">
            <label for="price">販売価格</label>
            <div style="display: flex; align-items: center;">
                <span style="margin-right: 0.5rem;">¥</span>
                <input type="number" name="price" id="price" value="{{ old('price') }}" min="1" required>
            </div>
            @error('price')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">出品する</button>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 300px; max-height: 300px;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

