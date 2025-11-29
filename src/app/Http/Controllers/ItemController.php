<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        // 自分が出品した商品は表示しない
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        // 検索機能
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->with(['user', 'categories', 'favorites'])->latest()->get();

        return view('items.index', compact('items'));
    }

    public function mylist(Request $request)
    {
        if (!Auth::check()) {
            return view('items.mylist', ['items' => collect()]);
        }

        // いいねした商品だけを取得
        $query = Auth::user()->favorites()->with('item.user', 'item.categories', 'item.favorites');

        // 検索機能
        if ($request->filled('search')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $favorites = $query->get();
        // いいねした商品のItemモデルのみを取得（nullを除外）
        $items = $favorites->map(function ($favorite) {
            return $favorite->item;
        })->filter();

        return view('items.mylist', compact('items'));
    }

    public function show($id)
    {
        $item = Item::with(['user', 'categories', 'favorites', 'comments.user'])->findOrFail($id);
        $isFavorite = Auth::check() && $item->favorites()->where('user_id', Auth::id())->exists();
        $favoriteCount = $item->favorites()->count();
        $commentCount = $item->comments()->count();

        return view('items.show', compact('item', 'isFavorite', 'favoriteCount', 'commentCount'));
    }
}
