<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AddressController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


Route::get('/',      [ItemController::class, 'index']);
Route::get('/search', [ItemController::class, 'search']);
Route::get('/login', [ProfileController::class, 'login'])->name('login');

// メール認証
Route::get('/email/verify', function () {
    return view('registerMail');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('auth', 'verified')->group(function () {
    // マイページ
    Route::get('/mypage',           [ItemController::class, 'mypage'])->name('mypage');
    Route::get('/mypage/profile',   [ProfileController::class, 'show'])->name('profile');
    Route::post('/mypage/profile',  [ProfileController::class, 'store']);
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);

    // 商品ページ
    Route::get('/item/{item_id}',           [ItemController::class, 'show'])->name('show');
    Route::post('/item/{item_id}/like',     [LikeController::class, 'store'])->name('like');
    Route::delete('/item/{item_id}/unlike', [LikeController::class, 'destroy'])->name('unlike');
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

    // 出品ページ
    Route::get('/sell',  [ItemController::class, 'getsell']);
    Route::post('/sell', [ItemController::class, 'postsell']);

    // 購入ページ
    Route::get('/purchase/{item_id}',         [ItemController::class, 'purchase'])->name('purchase');
    Route::get('/purchase/address/{item_id}', [ProfileController::class, 'edit'])->name('address');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'create'])->name('address.create');
    Route::post('/purchase/soldout/{item_id}',         [ItemController::class, 'soldout'])->name('soldout');
});
