<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID:2
     */

    public function test_user_login_fails_without_email()
    {
        User::factory()->make([
            'email' => 'test@example.com',
            'role' => 1
        ]);

        $this->post('/login', [
            'password' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('メールアドレスを入力してください', $errors['email'][0]);

    }

    public function test_user_login_fails_without_password(){
        User::factory()->make([
            'email' => 'test2@example.com',
            'role' => 1
        ]);

        $this->post('/login', [
            'email' => 'test2@example.com'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('パスワードを入力してください', $errors['password'][0]);
    }

    public function test_user_login_fails(){
        User::factory()->make([
            'email' => 'test2@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $this->post('/login', [
            'email' => 'test_test@example.com',
            'password' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('ログイン情報が登録されていません', $errors['email'][0]);
    }

    /**
     * ID:3
     */

    public function test_admin_login_fails_without_email()
    {
        User::factory()->make([
            'email' => 'admin_test@example.com',
            'password' => 'adminpass',
            'role' => 2
        ]);

        $this->post('/login', [
            'password' => 'adminpass'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('メールアドレスを入力してください', $errors['email'][0]);

    }

    public function test_admin_login_fails_without_password(){
        User::factory()->make([
            'email' => 'admin_test2@example.com',
            'role' => 2
        ]);

        $this->post('/login', [
            'email' => 'admin_test2@example.com'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('パスワードを入力してください', $errors['password'][0]);
    }

    public function test_admin_login_fails(){
        User::factory()->make([
            'email' => 'admin_test3@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('ログイン情報が登録されていません', $errors['email'][0]);
    }

}
