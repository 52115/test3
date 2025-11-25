@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="form-container">
    <h1>ログイン</h1>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">ログインする</button>
    </form>
    
    <p style="text-align: center; margin-top: 1rem;">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</div>
@endsection

