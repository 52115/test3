@extends('layouts.app')

@section('title', '会員登録')

@section('content')
<div class="form-container">
    <h1>会員登録</h1>
    
    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="@error('name') error @enderror">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="@error('email') error @enderror">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required class="@error('password') error @enderror">
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">確認用パスワード</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        
        <button type="submit" class="btn btn-primary">登録する</button>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</div>
@endsection

