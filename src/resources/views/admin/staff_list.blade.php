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
        @foreach($staffList as $user)
            <tr class="table-row">
                <td class="table-detail">{{ $user['name'] }}</td>
                <td class="table-detail email-area">{{ $user['email'] }}</td>
                <td class="table-detail">
                    <form action="{{ route('admin.staff_attendance_list',['id' => $user['id']]) }}" method="get" class="detail-form">
                        <button type="submit">詳細</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection