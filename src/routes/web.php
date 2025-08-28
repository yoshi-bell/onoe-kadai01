<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
    Route::post('/admin/delete/{contact_id}', [AdminController::class, 'delete'])->name('admin.delete');
    // 管理者ページの検索結果をCSVでエクスポート
    Route::get('/admin/export-csv', [AdminController::class, 'exportCsv'])->name('admin.export-csv');
});

//ログアウト時ログインページに移動
Route::post(
    '/logout',
    function () {
        Auth::logout();
        return redirect('/login');
    }
);
