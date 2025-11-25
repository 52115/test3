@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<div class="form-container">
    <h1>メール認証</h1>
    
    <p>登録したメールアドレス宛に認証メールを送信しました。</p>
    <p>メール認証を完了してください。</p>
    
    <div style="text-align: center; margin: 2rem 0;">
        @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
            <p style="margin-bottom: 1rem;">認証メールに記載されているリンクをクリックして認証を完了してください。</p>
            <p style="margin-bottom: 1rem;">メールが届かない場合は、下のボタンから再送してください。</p>
        @endif
    </div>
    
    <div style="text-align: center; margin: 2rem 0;">
        <p style="margin-bottom: 1rem;">認証メールに記載されている「認証はこちらから」リンクをクリックして認証を完了してください。</p>
        <a href="{{ route('auth.email-verification-notice') }}" class="btn btn-primary" style="display: inline-block; padding: 0.75rem 2rem; background: #e74c3c; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 1rem;">
            認証はこちらから
        </a>
        <p style="margin-top: 1rem; color: #666; font-size: 0.9rem;">※ メール認証サイトにアクセスするには、認証メールに記載されているリンクをクリックしてください。</p>
    </div>
    
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div style="text-align: center; margin-top: 1rem;">
            <button type="submit" style="background: none; border: none; color: #0066cc; text-decoration: underline; cursor: pointer;">
                認証メールを再送する
            </button>
        </div>
    </form>
</div>
@endsection

