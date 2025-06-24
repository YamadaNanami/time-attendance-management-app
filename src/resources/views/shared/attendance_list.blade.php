@extends('layouts.app')

<!-- 一般ユーザーと管理者用でタイトルの表示を切り替える　あとで対応すること -->
@section('title','勤怠一覧画面（一般ユーザー）')
<!-- @section('title','スタッフ別勤怠一覧画面（管理者）') -->


@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/attendance_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <!-- ロールによってpage-titleの表示を切り替える -->
    <h2 class="page-title">勤怠一覧</h2>
    <!-- スタッフ別勤怠一覧画面（管理者）のpage-title -->
    <!-- <h2 class="page-title">西玲奈さんの勤怠</h2> -->
    <form action="" method="" class="month-form">
        <button type="submit" class="month-btn" value="-1">
            <img src="{{ asset('img/arrow.svg') }}" alt="矢印" class="arrow-icon">
            前月
        </button>
        <div class="month-wrap">
            <img src="{{ asset('img/calendar.svg') }}" alt="カレンダーのアイコン" class="calendar-icon">
            <p class="selected-month">2023/06</p>
        </div>
        <button type="submit" class="month-btn" value="1">
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
        <!-- ここ１ヶ月分の日付（勤怠情報）が表示されるようにループさせる -->
        <tr class="table-row">
            <td class="table-detail">06/01(木)</td>
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
        <!-- ループここまで -->
        <!-- css確認用　あとで削除する -->
        <tr class="table-row">
            <td class="table-detail">06/01(木)</td>
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
    <!-- スタッフ別勤怠一覧画面（管理者）の時だけCSV出力のbtnを表示させる -->
    <form action="" class="csv-form">
        <button type="submit" class="csv-btn">CSV出力</button>
    </form>
</div>
@endsection