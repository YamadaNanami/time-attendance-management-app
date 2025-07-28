@extends('layouts.app')

@section('title')
    @if(Auth::user()->role == config('constants.ROLE.USER'))
        勤怠一覧画面（一般ユーザー）
    @elseif(Auth::user()->role == config('constants.ROLE.ADMIN'))
        スタッフ別勤怠一覧画面（管理者）
    @endif
@endsection


@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/attendance_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    @if(Auth::user()->role == config('constants.ROLE.USER'))
        <h2 class="page-title">勤怠一覧</h2>
        <form action="{{ route('user.attendance_list') }}" method="get" class="month-form">
            @elseif(Auth::user()->role == config('constants.ROLE.ADMIN'))
            <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>
            <form action="{{ route('admin.staff_attendance_list',['id' => $user->id]) }}" method="get" class="month-form">
    @endif
        <button type="submit" class="month-btn" name="monthBtn" value="subMonth">
            <img src="{{ asset('img/arrow.svg') }}" alt="矢印" class="arrow-icon">
            前月
        </button>
        <div class="month-wrap">
            <img src="{{ asset('img/calendar.svg') }}" alt="カレンダーのアイコン" class="calendar-icon">
            <p class="selected-month">{{ $selectedYear.'/'.$selectedMonth }}</p>
        </div>
        <button type="submit" class="month-btn" name="monthBtn" value="addMonth">
            翌月
            <img src="{{ asset('img/arrow.svg') }}" alt="矢印" class="arrow-icon next-arrow">
        </button>
    </form>
    <table class="list-table">
        <tr class="table-row">
            <th class="table-title">日付</th>
            <th class="table-title">出勤</th>
            <th class="table-title">退勤</th>
            <th class="table-title">休憩</th>
            <th class="table-title">合計</th>
            <th class="table-title">詳細</th>
        </tr>
        @foreach($timecards as $timecard)
            <tr class="table-row">
                <td class="table-detail">{{ $timecard['date'] }}({{ $timecard['weekdayLabel'] }})</td>
                <td class="table-detail">{{ $timecard['clockIn'] }}</td>
                <td class="table-detail">{{ $timecard['clockOut'] }}</td>
                <td class="table-detail">{{ $timecard['breakTime'] }}</td>
                <td class="table-detail">{{ $timecard['total'] }}</td>
                <td class="table-detail">
                    <form action="{{ route('detail', ['id' => $timecard['id']]) }}" method="get">
                        <input type="hidden" name="selectedDate" value="{{ $timecard['fullDate'] }}">
                        @if(Auth::user()->role == config('constants.ROLE.ADMIN'))
                            <input type="hidden" name="userId" value="{{ $user['id'] }}">
                        @endif
                        <button type="submit">詳細</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    @if(Auth::user()->role == config('constants.ROLE.ADMIN'))
        <form action="{{ route('admin.csv',['id' => $user->id]) }}" method="get" class="csv-form">
            <button type="submit" class="csv-btn">CSV出力</button>
        </form>
    @endif
</div>
@endsection