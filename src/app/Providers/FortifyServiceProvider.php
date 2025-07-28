<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
// use App\Actions\Fortify\ResetUserPassword;
// use App\Actions\Fortify\UpdateUserPassword;
// use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\RegisterController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\LoginController;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //ユーザ登録処理とログイン処理をカスタマイズしたクラスにバインドする（向き先変更）
        $bindings = [
            RegisteredUserController::class => RegisterController::class,
            AuthenticatedSessionController::class => LoginController::class
        ];

        foreach($bindings as $abstract => $concrete){
            $this->app->singleton($abstract, $concrete);
        }

        // ログアウト後にログイン画面に遷移する
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                $role = $request->role;
                if($role == 1){
                    // 一般ユーザーの場合はログイン画面（一般ユーザー）へリダイレクトする
                    return redirect()->route('login');
                }elseif($role == 2){
                    // 管理者の場合はログイン画面（管理者）へリダイレクトする
                    return redirect()->route('admin.login');
                }
            }
        });

        //ログイン後にトップ画面に遷移する
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $role = Auth::user()->role;
                if($role == 1){
                    // 一般ユーザーの場合は出勤登録画面（一般ユーザー）へリダイレクトする
                    return redirect()->route('user.index');
                }elseif($role == 2){
                    // 管理者の場合は勤怠一覧画面（管理者）へリダイレクトする
                    return redirect()->route('admin.attendance_list');
                }
            }
        });

        //会員登録直後に認証メールの送信＆メール認証誘導画面へ遷移する
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                $request->user()->sendEmailVerificationNotification();
                return redirect()->route('verification.notice');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(10)->by($throttleKey);
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

    }
}
