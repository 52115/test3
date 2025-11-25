@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2rem;">
    <div style="width: 100px; height: 100px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden;">
        @if($user->profile_image)
            <img src="{{ $user->profile_image }}" alt="プロフィール画像" style="width: 100%; height: 100%; object-fit: cover;">
        @else
            <span>画像</span>
        @endif
    </div>
    <div>
        <h1>{{ $user->name }}</h1>
        <a href="{{ route('profile.edit') }}" class="btn" style="background: #fff; border: 1px solid #e74c3c; color: #e74c3c;">プロフィールを編集</a>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('profile.index', ['page' => 'sell']) }}" class="tab {{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ route('profile.index', ['page' => 'buy']) }}" class="tab {{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
</div>

<div class="items-grid">
    @if($page === 'sell')
        @forelse($soldItems as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="item-image">
                    <div class="item-info">
                        <div class="item-name">{{ $item->name }}</div>
                        <div class="item-price">¥{{ number_format($item->price) }}</div>
                        @if($item->isSold())
                            <span class="sold-badge">Sold</span>
                        @endif
                    </div>
                </a>
            </div>
        @empty
            <p>出品した商品がありません</p>
        @endforelse
    @else
        @forelse($purchasedItems as $item)
            <div class="item-card">
                <a href="{{ route('items.show', $item->id) }}">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="item-image">
                    <div class="item-info">
                        <div class="item-name">{{ $item->name }}</div>
                        <div class="item-price">¥{{ number_format($item->price) }}</div>
                    </div>
                </a>
            </div>
        @empty
            <p>購入した商品がありません</p>
        @endforelse
    @endif
</div>
@endsection

