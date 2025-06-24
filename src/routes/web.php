<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ユーザー登録画面の表示
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// 一般ユーザー用ログイン画面の表示
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// 管理者用ログイン画面の表示
Route::get('/admin/login', function () {
    return view('auth.admin_login');
})->name('admin.login');

// メール認証誘導画面の表示
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 確認メールの再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// メール確認のハンドラ
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('user.attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');


// ⭐️一般ユーザー用のビューと一般ユーザー用のビューにアクセスできるロールを制御する（新たにミドルウェアを作成？）＆グループ化する

// 一般ユーザー用のビュー
// あとで書き直す（コントローラーで表示させる処理を記述する）
Route::get('/attendance',
function () {
    return view('user/index');
})->name('user.attendance');

// 管理者用のビュー
// あとで書き直す（コントローラーで表示させる処理を記述する）
Route::get('/admin/attendance/list', function () {
    return view('admin/attendance_list');
})->name('admin.attendance_list');
