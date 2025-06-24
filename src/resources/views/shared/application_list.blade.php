@extends('layouts.app')

@section('title','申請一覧画面（一般ユーザー）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/application_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">申請一覧</h2>
    <form action="" method="" class="tab-menu">
        <!-- checkedはあとで動的に変えられるように修正すること -->
        <input type="radio" onchange="submit(this.form)" name="approval_flag" id="unapproved" value="0" hidden checked>
        <label for="unapproved" class="tab-btn">承認待ち</label>
        <input type="radio" onchange="submit(this.form)" name="approval_flag" id="approved" value="1" hidden>
        <label for="approved">承認済み</label>
    </form>
    <table class="application-table">
        <tr class="table-row">
            <th class="table-title">状態</th>
            <th class="table-title">名前</th>
            <th class="table-title">対象日時</th>
            <th class="table-title">申請理由</th>
            <th class="table-title">申請日時</th>
            <th class="table-title">詳細</th>
        </tr>
        <!-- 一般ユーザーへの表示はログインユーザー名、管理者ユーザーへの表示は申請を出しているユーザー名を表示させる -->
        <tr class="table-row">
            <td class="table-detail">承認待ち</td>
            <td class="table-detail">西伶奈</td>
            <td class="table-detail">2023/06/01</td>
            <td class="table-detail comment-area">遅延のため</td>
            <td class="table-detail">2023/06/02</td>
            <td class="table-detail">
                <form action="" method="">
                    <button type="submit">詳細</button>
                </form>
            </td>
        </tr>
        <!-- css確認用　あとで削除する -->
        <tr class="table-row">
            <td class="table-detail">承認待ち</td>
            <td class="table-detail">西伶奈</td>
            <td class="table-detail">2023/06/01</td>
            <td class="table-detail comment-area">遅延のため</td>
            <td class="table-detail">2023/06/02</td>
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