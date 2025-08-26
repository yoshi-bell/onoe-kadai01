<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ユーザー向けのお問い合わせフォーム
Route::get('/', [ContactController::class, 'index']); // フォーム入力画面
Route::post('/confirm', [ContactController::class, 'confirm']); // 確認画面へ
Route::post('/thanks', [ContactController::class, 'store']); // データベース保存と完了画面
// 修正ボタン用のルートを追加
Route::post('/back', [ContactController::class, 'back']);

// 管理者向けのルート
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AuthController::class, 'admin']); // 管理画面
});

//テスト用ルート
Route::get('/thanks', function () {
    return view('thanks');
});