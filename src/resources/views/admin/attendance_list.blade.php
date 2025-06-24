@extends('layouts.app')

@section('title','勤怠一覧画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">2023年6月1日の勤怠</h2>
    <form action="" method="" class="date-form">
        <button type="submit" class="date-btn" value="-1">
            <img src="{{ asset('img/arrow.svg') }}" alt="矢印" class="arrow-icon">
            前日
        </button>
        <div class="date-wrap">
            <img src="{{ asset('img/calendar.svg') }}" alt="カレンダーのアイコン" class="calendar-icon">
            <p class="selected-date">2023/06/01</p>
        </div>
        <button type="submit" class="date-btn" value="1">
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
        <tr class="table-row">
            <td class="table-detail">山田 太郎</td>
            <td class="table-detail">09:00</td>
            <td class="table-detail">18:00</td>
            <td class="table-detail">1:00</td>
            <td class="table-detail">8:00</td>
            <td class="table-detail">
                <form action="" method="">
                    <button type="submit">詳細</button>
                </form>
            </td>
        </tr>
        <!-- css確認用　あとで削除する -->
        <tr class="table-row">
            <td class="table-detail">西 伶奈</td>
            <td class="table-detail">09:00</td>
            <td class="table-detail">18:00</td>
            <td class="table-detail">1:00</td>
            <td class="table-detail">8:00</td>
            <td class="table-detail">
                <form action="" method="">
                    <button type="submit">詳細</button>
                </form>
            </td>
        </tr>
        <!-- あとで削除する　ここまで -->
    </table>
</div>
@endsection