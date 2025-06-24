@extends('layouts.app')

@section('title','修正申請承認画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approval.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">勤怠詳細</h2>
    <form action="" class="approval-form">
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
            <tr class="table-row">
                <th class="table-title">出勤・退勤</th>
                <td class="table-detail">
                    <input type="time" name="time1" value="09:00" readonly>
                    〜
                    <input type="time" name="time2" value="18:00" readonly>
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">休憩</th>
                <td class="table-detail">
                    <input type="time" name="time3" value="12:00" readonly>
                    〜
                    <input type="time" name="time4" value="13:00" readonly>
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">休憩2</th>
                <td class="table-detail">
                    <input type="time" name="time5" value="" readonly>
                    〜
                    <input type="time" name="time6" value="" readonly>
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">備考</th>
                <td class="table-detail">
                    <input type="text" name="comment" class="comment-area" value="電車遅延のため" readonly>
                </td>
            </tr>
        </table>
        <!-- 承認前は以下のbtnを表示する -->
        <button type="submit" class="submit-btn">承認</button>
        <!-- 承認後は以下を表示する -->
        <!-- <button disabled class="disabled-btn">承認済み</button> -->
    </form>
</div>
@endsection