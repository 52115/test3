@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<div class="form-container">
    <h1>メール認証</h1>
    
    <p style="text-align: center; margin: 2rem 0;">
        メール認証が完了しました。
    </p>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('profile.initial-setup') }}" class="btn btn-primary" style="display: inline-block; padding: 0.75rem 2rem; background: #e74c3c; color: #fff; text-decoration: none; border-radius: 4px;">
            プロフィール設定へ進む
        </a>
    </div>
</div>
@endsection

