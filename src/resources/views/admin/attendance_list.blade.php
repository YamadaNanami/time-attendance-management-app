@extends('layouts.app')

@section('title','勤怠一覧画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">{{ $displayDateJa }}の勤怠</h2>
    <form action="{{ route(('admin.attendance_list')) }}" method="get" class="date-form">
        <button type="submit" class="date-btn" name="dayBtn" value="subDay">
            <img src="{{ asset('img/arrow.svg') }}" alt="矢印" class="arrow-icon">
            前日
        </button>
        <div class="date-wrap">
            <img src="{{ asset('img/calendar.svg') }}" alt="カレンダーのアイコン" class="calendar-icon">
            <p class="selected-date">{{ $selectedDay->format('Y/m/d') }}</p>
        </div>
        <button type="submit" class="date-btn" name="dayBtn" value="addDay">
            翌日
            <img src="{{ asset('img/arrow.svg') }}" alt="矢印" class="arrow-icon next-arrow">
        </button>
    </form>
    <table class="list-table">
        <tr class="table-row">
            <th class="table-title">名前</th>
            <th class="table-title">出勤</th>
            <th class="table-title">退勤</th>
            <th class="table-title">休憩</th>
            <th class="table-title">合計</th>
            <th class="table-title">詳細</th>
        </tr>
        @if(!empty($data))
            @foreach($data as $row)
                <tr class="table-row">
                    <td class="table-detail">{{ $row['name'] }}</td>
                    <td class="table-detail">{{ $row['clockIn'] }}</td>
                    <td class="table-detail">{{ $row['clockOut'] }}</td>
                    <td class="table-detail">{{ $row['breakTime'] }}</td>
                    <td class="table-detail">{{ $row['totalWorkTime'] }}</td>
                    <td class="table-detail">
                        <form action="{{ route('detail', ['id' => $row['id']]) }}" method="get">
                            <input type="hidden" name="userId" value="{{ $row['userId'] }}">
                            <button type="submit">詳細</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endif
    </table>
</div>
@endsection