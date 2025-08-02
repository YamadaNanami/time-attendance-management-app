@extends('layouts.app')

@section('title','メール認証画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/email-verified.css') }}">
@endsection

@section('content')
<div class="txt-wrap">
    <h2 class="page-title">ようこそ、{{ Auth::user()->name }}さん！</h2>
    <p class="txt">メール認証が完了しました。</p>
    <p class="txt">5秒後に勤怠登録画面へ遷移します。</p>
</div>
@endsection

@section('script')
<meta http-equiv="refresh" content="5; url={{ route('user.index') }}">
@endsection
