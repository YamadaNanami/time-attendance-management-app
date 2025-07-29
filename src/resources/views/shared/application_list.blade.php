@extends('layouts.app')

@section('title')
    @if(Auth::user()->role == config('constants.ROLE.USER'))
        申請一覧画面（一般ユーザー）
    @elseif(Auth::user()->role == config('constants.ROLE.ADMIN'))
        申請一覧画面（管理者）
    @endif
@endsection



@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/application_list.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">申請一覧</h2>
        <form action="{{ route('application_list') }}" method="get" class="tab-menu">
        <input type="radio" onchange="submit(this.form)" name="approvalFlag" id="unapproved" value="0" hidden {{ $approvalFlag == 0 ? 'checked' : '' }}>
        <label for="unapproved" class="tab-btn">承認待ち</label>
        <input type="radio" onchange="submit(this.form)" name="approvalFlag" id="approved" value="1" hidden {{ $approvalFlag == 1 ? 'checked' : '' }}>
        <label for="approved"  class="tab-btn">承認済み</label>
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
        @if(!empty($data))
            @foreach($data as $row)
                <tr class="table-row">
                    <td class="table-detail">{{ $approvalFlag == 0 ? '承認待ち' : '承認済み' }}</td>
                    <td class="table-detail">{{ $row['name'] }}</td>
                    <td class="table-detail">{{ $row['targetDate']->format('Y/m/d') }}</td>
                    <td class="table-detail comment-area">{{ $row['comment'] }}</td>
                    <td class="table-detail">{{ $row['corrected_date']->format('Y/m/d') }}</td>
                    <td class="table-detail">
                            @if(Auth::user()->role == config('constants.ROLE.USER'))
                                <form action="{{ route('detail', ['id' => $row['workDaysId']]) }}" method="get">
                                    <input type="hidden" name="selectedDate" value="{{ $row['targetDate'] }}">
                            @else
                                <form action="{{ route('admin.approval', ['attendance_correct_request' => $row['attendanceCorrectionId']]) }}" method="get">
                            @endif
                            <button type="submit">詳細</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endif
    </table>
</div>
@endsection