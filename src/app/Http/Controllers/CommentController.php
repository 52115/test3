<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($itemId);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'content' => $request->content,
        ]);

        return redirect()->route('items.show', $itemId)->with('success', 'コメントを投稿しました');
    }
}
