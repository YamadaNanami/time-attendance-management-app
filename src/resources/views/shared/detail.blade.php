@extends('layouts.app')

@section('title')
    @if(Auth::user()->role == config('constants.ROLE.USER'))
        勤怠詳細画面（一般ユーザー）
    @elseif(Auth::user()->role == config('constants.ROLE.ADMIN'))
        勤怠詳細画面（管理者）
    @endif
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/detail.css') }}">
@endsection

@section('content')
@php
    $isReadonly = !is_null($data['approvalFlag']) && $data['approvalFlag'] == config('constants.APPROVAL_FLAG.UNAPPROVED');
@endphp
<div class="content-wrap">
    <h2 class="page-title">勤怠詳細</h2>
    <form action="{{ route('attendance_correction',['id' => $data['id'] ?? 0]) }}" method="post" class="detail-form" novalidate>
        @csrf
        @if(Auth::user()->role == config('constants.ROLE.ADMIN'))
            <input type="hidden" name="userId" value="{{ request()->query('userId') }}">
        @endif
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
                    <input type="time" name="clockIn" value="{{ old('clockIn',$data['clockIn']) }}" {{$isReadonly ? 'readonly' : ''}}>
                    〜
                    <input type="time" name="clockOut" value="{{ old('clockOut',$data['clockOut']) }}" {{$isReadonly ? 'readonly' : ''}}>
                    @error('clockIn')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
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
                            <input type="time" name="breakIn[{{ $loop->iteration }}]" value="{{ old('breakIn.'.$loop->iteration,$break['in']) }}" {{$isReadonly ? 'readonly' : ''}}>
                            〜
                            <input type="time" name="breakOut[{{ $loop->iteration  }}]" value="{{ old('breakOut.'.$loop->iteration,$break['out']) }}" {{$isReadonly ? 'readonly' : ''}}>
                            @if($errors->has('breakIn.'.$loop->iteration))
                                <p class="error-msg">{{ $errors->first('breakIn.'.$loop->iteration) }}</p>
                            @elseif($errors->has('breakOut.'.$loop->iteration))
                                <p class="error-msg">{{ $errors->first('breakOut.'.$loop->iteration) }}</p>
                            @endif
                        </td>
                    </tr>
                    @if($index == array_key_last($breaks))
                    {{-- 空の追加行 --}}
                    <tr class="table-row">
                            <th class="table-title">休憩{{ $index + 1 }}</th>
                            <td class="table-detail">
                                <input type="time" name="breakIn[{{ $index + 1 }}]" value="{{ old('breakIn.'.$index + 1) }}" {{$isReadonly ? 'readonly' : ''}}>
                                〜
                                <input type="time" name="breakOut[{{ $index + 1 }}]" value="{{ old('breakOut.'.$index + 1) }}" {{$isReadonly ? 'readonly' : ''}}>
                                @if($errors->has('breakIn.'.$index + 1))
                                    <p class="error-msg">{{ $errors->first('breakIn.'.$index + 1) }}</p>
                                @elseif($errors->has('breakOut.'.$index + 1))
                                    <p class="error-msg">{{ $errors->first('breakOut.'.$index + 1) }}</p>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            @else
                {{-- 休憩時間が打刻されていない(休憩が1行のみ)場合 --}}
                <tr class="table-row">
                        <th class="table-title">休憩</th>
                        <td class="table-detail">
                            <input type="time" name="breakIn[1]" value="{{ old('breakIn.1') }}" {{$isReadonly ? 'readonly' : ''}}>
                            〜
                            <input type="time" name="breakOut[1]" value="{{ old('breakOut.1') }}" {{$isReadonly ? 'readonly' : ''}}>
                            @if($errors->has('breakIn.1'))
                                <p class="error-msg">{{ $errors->first('breakIn.1') }}</p>
                            @elseif($errors->has('breakOut.1'))
                                <p class="error-msg">{{ $errors->first('breakOut.1') }}</p>
                            @endif
                        </td>
                    </tr>
            @endif
            <tr class="table-row">
                <th class="table-title">備考</th>
                <td class="table-detail">
                    <textarea name="comment" class="comment-area" {{$isReadonly ? 'readonly' : ''}}>{{ old('comment',$data['comment']) }}</textarea>
                    @error('comment')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        </table>
        @if($isReadonly)
        <!-- 「*承認待ちのため修正はできません。」が管理者側に表示されると違和感があったため、管理者向けの文言を追加 -->
            @if(Auth::user()->role == config('constants.ROLE.USER'))
                <p class="text">*承認待ちのため修正はできません。</p>
            @elseif(Auth::user()->role == config('constants.ROLE.ADMIN'))
                <p class="text">
                    *修正申請中の勤怠情報です。
                    <a href="{{ route('admin.approval',['attendance_correct_request' => $data['attendanceCorrectionId']]) }}" class="approval-link">
                        修正申請承認画面はこちら
                    </a>
                </p>
            @endif
        @else
            <button type="submit" class="submit-btn" name="selectedDate" value="{{ $data['year'].'-'.$data['month'].'-'.$data['day'] }}">修正</button>
        @endif
    </form>
</div>
@endsection