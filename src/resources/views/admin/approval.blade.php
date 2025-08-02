@extends('layouts.app')

@section('title','修正申請承認画面（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approval.css') }}">
@endsection

@section('content')
<div class="content-wrap">
    <h2 class="page-title">勤怠詳細</h2>
    <div class="table-wrap">
        <table class="detail-table">
            <tr class="table-row">
                <th class="table-title">名前</th>
                <td class="table-detail">{{ $data['name'] }}</td>
            </tr>
            <tr class="table-row">
                <th class="table-title">日付</th>
                <td class="table-detail flex-wrap">
                    <p class="year">{{ $data['year'].'年' }}</p>
                    <p>{{ $data['month'].'月'.$data['day'].'日' }}</p>
                </td>
            </tr>
            <tr class="table-row">
                <th class="table-title">出勤・退勤</th>
                <td class="table-detail">
                    <input type="time" name="clockIn" value="{{ $data['clockIn'] }}" readonly>
                    〜
                    <input type="time" name="clockOut" value="{{ $data['clockOut'] }}" readonly>
                </td>
            </tr>
            @php
                $breaks = $data['breakTimeLists'] ?? [];
            @endphp
            @if(!empty($breaks))
                @foreach($breaks as $index => $break)
                    <tr class="table-row">
                        <th class="table-title">休憩{{ $loop->iteration == 1 ? '' : $loop->iteration }}</th>
                        <td class="table-detail">
                            <input type="time" name="breakIn[{{ $loop->iteration }}]" value="{{ old('breakIn.'.$loop->iteration,$break['in']) }}" readonly>
                            〜
                            <input type="time" name="breakOut[{{ $loop->iteration  }}]" value="{{ old('breakOut.'.$loop->iteration,$break['out']) }}" readonly>
                        </td>
                    </tr>
                    @if($index == array_key_last($breaks))
                    {{-- 空の追加行 --}}
                        <tr class="table-row">
                            <th class="table-title">休憩{{ $index + 1 }}</th>
                        </tr>
                    @endif
                @endforeach
            @else
                {{-- 休憩時間が打刻されていない場合 --}}
                <tr class="table-row">
                        <th class="table-title">休憩</th>
                    </tr>
            @endif
            <tr class="table-row">
                <th class="table-title">備考</th>
                <td class="table-detail">
                    <input type="text" name="comment" class="comment-area" value="{{ $data['comment'] }}" readonly>
                </td>
            </tr>
        </table>
        @if($data['approvalFlag'] == config('constants.APPROVAL_FLAG.UNAPPROVED'))
            {{-- 承認前は以下のbtnを表示する --}}
            <form action="{{ route('admin.approval.store_update',['attendance_correct_request' => $data['attendanceCorrectionId']]) }}" method="post">
                @csrf
                <button type="submit" class="submit-btn">承認</button>
            </form>
        @elseif($data['approvalFlag'] == config('constants.APPROVAL_FLAG.APPROVED'))
            {{-- 承認後は以下を表示する --}}
            <button disabled class="disabled-btn">承認済み</button>
        @endif
    </div>
</div>
@endsection