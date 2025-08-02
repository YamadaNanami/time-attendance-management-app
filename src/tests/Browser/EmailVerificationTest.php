<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EmailVerificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * ID:16
     */
    public function test_success_send_email(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('input[name="name"]', 'テスト 太郎')
                ->type('input[name="email"]', 'test@example.com')
                ->type('input[name="password"]', 'password')
                ->type('input[name="password_confirmation"]', 'password')
                ->press('登録する')
                ->waitFor('.verify-link')
                ->assertTitle('メール認証誘導画面')
                ->click('.verify-link') //「認証はこちらから」ボタンを押下
                ->waitForTextIn('.page-title','ようこそ、テスト 太郎さん！')
                ->assertTitle('メール認証画面') //メール認証画面の表示確認
                ->waitFor('.status')
                ->assertTitle('勤怠登録画面（一般ユーザー）'); //勤怠登録画面の表示確認
        });

    }
}
