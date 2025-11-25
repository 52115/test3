@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="tabs">
    <a href="{{ route('items.index', request()->only('search')) }}" class="tab active">おすすめ</a>
    <a href="{{ route('items.mylist', request()->only('search')) }}" class="tab">マイリスト</a>
</div>

<div class="items-grid">
    @forelse($items as $item)
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
        <p>商品がありません</p>
    @endforelse
</div>
@endsection

