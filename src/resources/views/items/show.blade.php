@extends('layouts.app')

@section('title', $item->name)

@section('content')
<div class="item-detail">
    <div>
        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="item-detail-image">
    </div>
    <div class="item-detail-info">
        <h1>{{ $item->name }}</h1>
        @if($item->brand_name)
            <p>ブランド名: {{ $item->brand_name }}</p>
        @endif
        <div class="item-detail-price">¥{{ number_format($item->price) }}(税込)</div>
        
        <div class="item-actions">
            <form action="{{ route('favorite.toggle', $item->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="favorite-btn {{ $isFavorite ? 'active' : '' }}">
                    ♥ {{ $favoriteCount }}
                </button>
            </form>
            <span>💬 {{ $commentCount }}</span>
        </div>
        
        @auth
            @if($item->user_id !== auth()->id() && !$item->isSold())
                <a href="{{ route('purchase.show', $item->id) }}" class="btn btn-primary">購入手続きへ</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">購入手続きへ</a>
        @endauth
        
        <div style="margin-top: 2rem;">
            <h2>商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>
        
        <div style="margin-top: 2rem;">
            <h2>商品の情報</h2>
            <p>カテゴリー: 
                @foreach($item->categories as $category)
                    <span style="background: #ddd; padding: 0.25rem 0.5rem; border-radius: 4px; margin-right: 0.5rem;">{{ $category->name }}</span>
                @endforeach
            </p>
            <p>商品の状態: {{ $item->condition }}</p>
        </div>
        
        <div style="margin-top: 2rem;">
            <h2>コメント({{ $commentCount }})</h2>
            @foreach($item->comments as $comment)
                <div style="margin: 1rem 0; padding: 1rem; background: #f5f5f5; border-radius: 4px;">
                    <p><strong>{{ $comment->user->name }}</strong></p>
                    <p>{{ $comment->content }}</p>
                </div>
            @endforeach
            
            @auth
                <form action="{{ route('comment.store', $item->id) }}" method="POST" style="margin-top: 1rem;">
                    @csrf
                    <div class="form-group">
                        <label>商品へのコメント</label>
                        <textarea name="content" required></textarea>
                        @error('content')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">コメントを送信する</button>
                </form>
            @endauth
        </div>
    </div>
</div>
@endsection

