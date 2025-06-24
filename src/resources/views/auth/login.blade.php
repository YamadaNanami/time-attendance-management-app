@extends('layouts.app')

@section('title','ログイン画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="auth-page-title">ログイン</h2>
    <form action="/login" method="post" class="form-wrap" novalidate>
        @csrf
        <label for="email" class="form-label">メールアドレス</label>
        <input type="email" name="email" id="email" class=" form-input">
        @error('email')
        <p class="error-msg">{{ $message }}</p>
        @enderror
        <label for="password" class="form-label">パスワード</label>
        <input type="password" name="password" id="password" class="form-input">
        @error('password')
        <p class="error-msg last-input-msg">{{ $message }}</p>
        @enderror
        <button type="submit" class="form-btn">ログインする</button>
    </form>
    <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
</div>
@endsection