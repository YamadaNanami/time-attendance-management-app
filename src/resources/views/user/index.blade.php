@extends('layouts.app')

@section('title','勤怠登録画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <div class="status">勤務外</div>
    <div class="date">2023年6月1日(木)</div>
    <div class="time">08:00</div>
    <form action="" method="" class="form-wrap">
        <button type="submit" value="1" class="form-btn">出勤</button>
        {{--出勤中の場合のbtn表示--}}
        <!-- <div class="flex-wrap">
            <button type="submit" value="4" class="form-btn">退勤</button>
            <button type="submit" value="2" class="form-btn bg-white">休憩入</button>
        </div> -->
        {{--休憩中の場合のbtn表示--}}
        <!-- <button type="submit" value="3" class="form-btn bg-white">休憩戻</button> -->
    </form>
    {{--退勤後のみ下記を表示させる--}}
    <!-- <p class="text">お疲れ様でした。</p> -->
</div>
@endsection