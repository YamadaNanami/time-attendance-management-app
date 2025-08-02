<?php

use App\Http\Controllers\AdminAttendanceListController;
use App\Http\Controllers\AdminStaffListController;
use App\Http\Controllers\ApplicationListController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\CustomVerifyEmailController;
use App\Http\Controllers\DetailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ログイン画面（一般ユーザー）の表示
Route::view('/login', 'auth.login',['isAdminView' => false])->name('login');

// ログイン画面（管理者）の表示
Route::view('/admin/login', 'auth.login',['isAdminView' => true])->name('admin.login');

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
Route::get('/email/verify/{id}/{hash}',CustomVerifyEmailController::class)->middleware(['auth', 'signed'])->name('verification.verify');

// メール認証画面の表示
Route::get('/email/verified', function () {
    return view('auth.email-verified');
})->name('email.verified');


// 一般ユーザー用のビュー
Route::middleware(['role:user'])->group(function () {
    Route::name('user.')->group(function () {
        Route::group(['prefix' => 'attendance'], function () {
            // 勤怠登録画面の表示
            Route::get('/',[AttendanceController::class,'index'])
                ->name('index');

            // 勤怠情報の登録処理
            Route::post('/', [AttendanceController::class, 'createTimecard'])
                ->name('createTimecard');

            // 勤怠一覧画面の表示
            Route::get('list', [AttendanceListController::class, 'index'])
                ->name('attendance_list');
        });
    });
});


// 管理者用のビュー
Route::middleware(['role:admin'])->group(function () {
    Route::name('admin.')->group(function () {
        Route::group(['prefix' => 'admin'], function () {
            Route::group(['prefix' => 'attendance'], function () {
                // 勤怠一覧画面の表示
                Route::get('list', [AdminAttendanceListController::class, 'index'])
                    ->name('attendance_list');

                // スタッフ別勤怠一覧画面（管理者）の表示
                Route::get('staff/{id}', [AttendanceListController::class, 'adminIndex'])
                    ->name('staff_attendance_list');
            });

            // CSV出力
            Route::get('export-csv/{id}', [AttendanceListController::class, 'exportCsv'])
                ->name('csv');

            // スタッフ一覧画面の表示
            Route::get('staff/list', [AdminStaffListController::class, 'index'])
                ->name('staff_list');
        });

        Route::group(['prefix' => '/stamp_correction_request/approve/{attendance_correct_request}'], function () {
            // 修正申請承認画面の表示
            Route::get('/',[AttendanceDetailController::class,'index'])
            ->name('approval');

            // 修正申請の承認
            Route::post('/',[ApprovalController::class,'storeAndUpdate'])
            ->name('approval.store_update');
        });
    });
});


// 共通のルーティング
Route::middleware('auth')->group(function () {
    // 申請一覧画面の表示
    Route::get('/stamp_correction_request/list', [ApplicationListController::class, 'index'])
        ->name('application_list');

    Route::group(['prefix' => 'attendance'], function () {
        // 勤怠詳細画面の表示
        Route::get('{id}', [AttendanceDetailController::class, 'index'])
            ->name('detail');

        // 勤怠情報の修正
        Route::post('{id?}', [DetailController::class, 'createAttendanceCorrection'])
            ->name('attendance_correction');
    });
});
