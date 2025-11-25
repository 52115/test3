<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 'sell');

        $soldItems = $user->items()->get();
        $purchasedItems = $user->purchasedItems()->get();

        return view('profile.index', compact('user', 'page', 'soldItems', 'purchasedItems'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // プロフィール画像のアップロード
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                $oldPath = str_replace('/storage/', '', $user->profile_image);
                Storage::disk('public')->delete($oldPath);
            }
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image'] = '/storage/' . $imagePath;
        }

        $user->update($data);

        return redirect()->route('profile.index')->with('success', 'プロフィールを更新しました');
    }

    public function initialSetup()
    {
        return view('profile.initial-setup');
    }

    public function storeInitialSetup(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // プロフィール画像のアップロード
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image'] = '/storage/' . $imagePath;
        }

        $user->update($data);

        return redirect()->route('items.index')->with('success', 'プロフィールを設定しました');
    }
}
