<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('sell.create', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 画像アップロード
        $imagePath = $request->file('image')->store('items', 'public');
        $imageUrl = '/storage/' . $imagePath;

        // 商品作成
        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'brand_name' => $request->brand_name,
            'price' => $request->price,
            'condition' => $request->condition,
            'image_url' => $imageUrl,
        ]);

        // カテゴリの紐付け
        $item->categories()->attach($request->categories);

        return redirect()->route('items.index')->with('success', '商品を出品しました');
    }
}
