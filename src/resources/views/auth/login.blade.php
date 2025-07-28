@extends('layouts.app')

@section('title')
    @if($isAdminView)
        ログイン画面（管理者）
    @else
        ログイン画面（一般ユーザー）
    @endif
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    @if($isAdminView)
        <h2 class="auth-page-title">管理者ログイン</h2>
    @else
        <h2 class="auth-page-title">ログイン</h2>
    @endif
    <form action="/login" method="post" class="form-wrap" novalidate>
        @csrf
        @if($isAdminView)
            <input type="hidden" name="isAdminView" value={{ $isAdminView }}>
        @endif
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
        @if($isAdminView)
            <button type="submit" class="form-btn">管理者ログインする</button>
        @else
            <button type="submit" class="form-btn">ログインする</button>
        @endif
    </form>
    @if(!$isAdminView)
        <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
    @endif
</div>
@endsection