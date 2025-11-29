@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<div class="form-container">
    <h1>メール認証</h1>
    
    @if (session('status'))
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
            {{ session('status') }}
        </div>
    @endif
    
    <p>登録したメールアドレス宛に認証メールを送信しました。</p>
    <p>メール認証を完了してください。</p>
    
    <div style="text-align: center; margin: 2rem 0;">
        @if(auth()->check() && !auth()->user()->hasVerifiedEmail())
            <p style="margin-bottom: 1rem;">認証メールに記載されているリンクをクリックして認証を完了してください。</p>
            <p style="margin-bottom: 1rem;">メールが届かない場合は、下のボタンから再送してください。</p>
        @endif
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

