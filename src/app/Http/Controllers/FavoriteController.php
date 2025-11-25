<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request, $itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($itemId);
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('item_id', $itemId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'item_id' => $itemId,
            ]);
            $isFavorite = true;
        }

        $favoriteCount = $item->favorites()->count();

        if ($request->ajax()) {
            return response()->json([
                'isFavorite' => $isFavorite,
                'favoriteCount' => $favoriteCount,
            ]);
        }

        return back();
    }
}
