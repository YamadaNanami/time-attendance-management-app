@extends('layouts.app')

@section('title','メール認証誘導画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="txt-wrap">
    <div class="txt-area">
        <p class="txt">登録していただいたメールアドレスに認証メールを送付しました</p>
        <p class="txt">メール認証を完了してください。</p>
    </div>
    <a href="{{ URL::temporarySignedRoute('verification.verify',now()->addMinutes(60),['id' => Auth::id(), 'hash' => sha1(Auth::user()->email)]) }}" class="verify-link">認証はこちらから</a>
    <form action="{{ route('verification.send') }}" method="post" class="send-form">
        @csrf
        <input type="submit" class="send-link" value="認証メールを再送する">
    </form>
</div>
@endsection