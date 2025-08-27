<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ユーザー向けのお問い合わせフォーム
Route::get('/', [ContactController::class, 'index']);
Route::post('/confirm', [ContactController::class, 'confirm']);
Route::post('/thanks', [ContactController::class, 'store']);
Route::post('/back', [ContactController::class, 'back']);

// 管理者向けのルート
Route::middleware('auth')->group(function () {
    // AuthControllerのadminルートを削除し、AdminControllerに統一
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
});

//テスト用ルート 最後に消す
Route::get('/thanks', function () {
    return view('thanks');
});