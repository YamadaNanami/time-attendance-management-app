@extends('layouts.app')

<!-- 一般ユーザーと管理者用でタイトルの表示を切り替える　あとで対応すること -->
@section('title','勤怠詳細画面（一般ユーザー）')
<!-- @section('title','勤怠詳細画面（管理者）') -->

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/detail.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">勤怠詳細</h2>
    <form action="" class="detail-form">
        <table class="detail-table">
            <tr class="table-row">
                <th class="table-title">名前</th>
                <td class="table-detail">西　伶奈</td>
            </tr>
            <tr class="table-row">
                <th class="table-title">日付</th>
                <td class="table-detail flex-wrap">
                    <p class="year">2023年</p>
                    <p>6月1日</p>
                </td>
            </tr>
            <!-- 承認待ちの場合は、入力不可＆枠線非表示にさせること -->
            <tr class="table-row">
                <th class="table-title">出勤・退勤</th>
                <td class="table-detail">
                    <input type="time" name="time1" value="09:00">
                    〜
                    <input type="time" name="time2" value="18:00">
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">休憩</th>
                <td class="table-detail">
                    <input type="time" name="time3" value="12:00">
                    〜
                    <input type="time" name="time4" value="13:00">
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">休憩2</th>
                <td class="table-detail">
                    <input type="time" name="time5" value="">
                    〜
                    <input type="time" name="time6" value="">
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">備考</th>
                <td class="table-detail">
                    <textarea name="comment" class="comment-area">電車遅延のため</textarea>
                </td>
            </tr>
        </table>
        <!-- 承認待ちの場合は、btnを非表示に、textを表示 -->
        <button type="submit" class="submit-btn">修正</button>
        <!-- <p class="text">*承認待ちのため修正はできません。</p> -->
    </form>
</div>
@endsection