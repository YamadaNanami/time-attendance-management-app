<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * ID:1
     */
    public function test_registration_fails_without_name()
    {
        $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('お名前を入力してください', $errors['name'][0]);
    }

    public function test_registration_fails_without_email()
    {
        $this->post('/register', [
            'name' => 'テスト 太郎',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('メールアドレスを入力してください', $errors['email'][0]);
    }

    public function test_registration_fails_min_password()
    {
        $this->post('/register', [
            'name' => '山田　太郎',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('パスワードは8文字以上で入力してください', $errors['password'][0]);
    }

    public function test_registration_fails_confirmed_password()
    {
        $this->post('/register', [
            'name' => '山田　太郎',
            'email' => 'test@example.com',
            'password' => '12345678',
            'password_confirmation' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('パスワードと一致しません', $errors['password'][0]);
    }

    public function test_registration_fails_without_password()
    {
        $this->post('/register', [
            'name' => '山田　太郎',
            'email' => 'test@example.com',
            'password_confirmation' => 'password'
        ]);

        $errors = session('errors')->getBag('default')->getMessages();

        $this->assertEquals('パスワードを入力してください', $errors['password'][0]);
    }

    public function test_registration_success()
    {
        $this->post('/register', [
            'name' => '山田　太郎',
            'email' => 'test_test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test_test@example.com']);
    }

}
