<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use Illuminate\Support\Facades\Route;

// 商品一覧
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/mylist', [ItemController::class, 'mylist'])->name('items.mylist');
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');

// いいね機能
Route::post('/favorite/{itemId}', [FavoriteController::class, 'toggle'])->name('favorite.toggle')->middleware('auth');

// コメント機能
Route::post('/comment/{itemId}', [CommentController::class, 'store'])->name('comment.store')->middleware('auth');

// 商品出品
Route::get('/sell', [SellController::class, 'create'])->name('sell.create')->middleware('auth');
Route::post('/sell', [SellController::class, 'store'])->name('sell.store')->middleware('auth');

// 商品購入
Route::get('/purchase/{itemId}', [PurchaseController::class, 'show'])->name('purchase.show')->middleware('auth');
Route::get('/purchase/address/{itemId}', [PurchaseController::class, 'address'])->name('purchase.address')->middleware('auth');
Route::post('/purchase/address/{itemId}', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress')->middleware('auth');
Route::post('/purchase/{itemId}', [PurchaseController::class, 'store'])->name('purchase.store')->middleware('auth');

// プロフィール
Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index')->middleware('auth');
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');
Route::get('/profile/setup', [ProfileController::class, 'initialSetup'])->name('profile.initial-setup')->middleware('auth');
Route::post('/profile/setup', [ProfileController::class, 'storeInitialSetup'])->name('profile.store-initial-setup')->middleware('auth');

// メール認証
Route::get('/email/verify', \App\Http\Controllers\Auth\EmailVerificationPromptController::class)
    ->middleware('auth')
    ->name('auth.email-verification-notice');
