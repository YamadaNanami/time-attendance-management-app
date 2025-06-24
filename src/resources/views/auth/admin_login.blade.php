@extends('layouts.app')

@section('title','ログイン画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/admin_login.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="auth-page-title">管理者ログイン</h2>
    <form action="/login" method="post" class="form-wrap" novalidate>
        @csrf
        <input type="hidden" name="isAdminView" value="true">
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
        <button type="submit" class="form-btn">管理者ログインする</button>
    </form>
</div>
@endsection