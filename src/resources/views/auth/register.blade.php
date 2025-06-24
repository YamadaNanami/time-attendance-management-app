@extends('layouts.app')

@section('title','会員登録画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="auth-page-title">会員登録</h2>
    <form action="/register" method="post" class="form-wrap" novalidate>
        @csrf
        <input type="hidden" name="role" value=1>
        <label for="name" class="form-label">名前</label>
        <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
        @error('name')
        <p class="error-msg">{{ $message }}</p>
        @enderror
        <label for="email" class="form-label">メールアドレス</label>
        <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}">
        @error('email')
        <p class="error-msg">{{ $message }}</p>
        @enderror
        <label for="password" class="form-label">パスワード</label>
        <input type="password" name="password" id="password" class="form-input">
        @error('password')
        <p class="error-msg">{{ $message }}</p>
        @enderror
        <label for="conf-password" class="form-label">パスワード確認</label>
        <input type="password" name="password_confirmation" id="conf-password" class="form-input">
        <button type="submit" class="form-btn">登録する</button>
    </form>
    <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
</div>
@endsection