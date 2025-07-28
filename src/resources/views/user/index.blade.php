@extends('layouts.app')

@section('title','勤怠登録画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <div class="status">{{ $status }}</div>
    @livewire('attendance')
    <form action="{{ route('user.createTimecard') }}" method="post" class="form-wrap">
        @csrf
        @switch ($status)
            @case('勤務外')
                <button type="submit" name="type" value="1" class="form-btn">出勤</button>
                @break
            @case('出勤中')
                <div class="flex-wrap">
                    <button type="submit" name="type" value="4" class="form-btn">退勤</button>
                    <button type="submit" name="type" value="2" class="form-btn bg-white">休憩入</button>
                </div>
                @break
            @case('休憩中')
                <button type="submit" name="type" value="3" class="form-btn bg-white">休憩戻</button>
                @break
        @endswitch
    </form>
    @if($status == '退勤済')
    <p class="text">お疲れ様でした。</p>
    @endif
</div>
@endsection