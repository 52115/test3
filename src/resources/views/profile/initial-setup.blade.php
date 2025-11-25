@extends('layouts.app')

@section('title', 'プロフィール設定')

@section('content')
<div class="form-container">
    <h1>プロフィール設定</h1>
    
    <form method="POST" action="{{ route('profile.store-initial-setup') }}" enctype="multipart/form-data" novalidate>
        @csrf
        
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
            <div style="width: 100px; height: 100px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <span id="profile-preview">画像</span>
            </div>
            <div>
                <label for="profile_image" class="btn" style="background: #fff; border: 1px solid #e74c3c; color: #e74c3c; cursor: pointer;">画像を選択する</label>
                <input type="file" name="profile_image" id="profile_image" accept="image/*" style="display: none;" onchange="previewProfileImage(this)">
                @error('profile_image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required class="@error('name') error @enderror">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}" class="@error('postal_code') error @enderror">
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', auth()->user()->address) }}" class="@error('address') error @enderror">
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="building_name">建物名</label>
            <input type="text" id="building_name" name="building_name" value="{{ old('building_name', auth()->user()->building_name) }}" class="@error('building_name') error @enderror">
            @error('building_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">更新する</button>
    </form>
</div>

<script>
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profile-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

