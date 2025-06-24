@extends('layouts.app')

@section('title','スタッフ一覧画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">スタッフ一覧</h2>
    <table class="staff-table">
        <tr class="table-row">
            <th class="table-title">名前</th>
            <th class="table-title email-head">メールアドレス</th>
            <th class="table-title">月次勤怠</th>
        </tr>
        <tr class="table-row">
            <td class="table-detail">西 伶奈</td>
            <td class="table-detail email-area">reina.n@coachtech.com</td>
            <td class="table-detail">
                <form action="" class="detail-form">
                    <button type="submit">詳細</button>
                </form>
            </td>
        </tr>
        <!-- css確認用　あとで削除する -->
        <tr class="table-row">
            <td class="table-detail">山田 太郎</td>
            <td class="table-detail email-area">keikichi.y@coachtech.com</td>
            <td class="table-detail">
                <form action="" class="detail-form">
                    <button type="submit">詳細</button>
                </form>
            </td>
        </tr>
        <!-- あとで削除する　ここまで -->
    </table>
</div>
@endsection