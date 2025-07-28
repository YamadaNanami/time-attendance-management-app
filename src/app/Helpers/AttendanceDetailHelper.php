<?php

namespace App\Helper;

use App\Models\AttendanceCorrectionDetail;
use App\Models\Timecard;

class AttendanceDetailHelper{ // 詳細画面表示用のクラス
    const START_INDEX = 0;
    const TIME_MAX_LENGTH = 5;

    public static function getAttendanceData($attendanceCorrection): array{ // 修正申請データと修正前の勤怠データを取得する
        // 修正申請データを取得する
        $corrections = $attendanceCorrection->details
            ->sortBy('corrected_time')
            ->groupBy('type');

        // 修正前の勤怠データを取得する
        $targetUserId = $attendanceCorrection->user_id;
        $targetDate = $attendanceCorrection->target_date;
        $timecards = Timecard::where('user_id', $targetUserId)
            ->where('date', $targetDate);

        $isNew = !$timecards->exists();

        // 表示用データの作成
        $clockTimeLists = [];
        $breakTimeLists = [];
        $breakInList = [];
        $breakOutList = [];

        if($isNew){
            // 修正申請時に新規登録した勤怠データの場合
            $new = self::getNewlyCreatedCorrectionDetails($corrections,$clockTimeLists,$breakTimeLists,$breakInList,$breakOutList);

            $clockTimeLists = $new['clockTimeLists'];
            $breakTimeLists = $new['breakTimeLists'];
        }else{
            // 既存の勤怠情報の修正申請がある場合
            $records = self::getEffectiveAttendanceRecords($timecards,$targetUserId,$targetDate,$attendanceCorrection->id,$clockTimeLists,$breakTimeLists);

            $clockTimeLists = $records['clockTimeLists'];
            $breakTimeLists = $records['breakTimeLists'];
        }

        return [
            'clockIn' => $clockTimeLists[config('constants.TIME_TYPE.CLOCK_IN')]['time'],
            'clockOut' => $clockTimeLists[config('constants.TIME_TYPE.CLOCK_OUT')]['time'],
            'breakTimeLists' => $breakTimeLists,
            'name' => $attendanceCorrection->user->name,
            'comment' => $attendanceCorrection->comment,
            'approvalFlag' => $attendanceCorrection->approval_flag,
            'targetDate' => $targetDate
        ];
    }

    public static function formatTime(string $time): string{
        return substr($time, self::START_INDEX, self::TIME_MAX_LENGTH);
    }

    public static function getNewlyCreatedCorrectionDetails($corrections,$clockTimeLists,$breakTimeLists,$breakInList,$breakOutList){ // 修正申請時に新規登録した勤怠データを取得する
        foreach($corrections as $type => $group){
            if(in_array($type,[config('constants.TIME_TYPE.CLOCK_IN'),config('constants.TIME_TYPE.CLOCK_OUT')])){
                $time = $group->first()?->corrected_time;
                $clockTimeLists[$type] = [
                    'time' => self::formatTime($time)
                ];
            }elseif($type == config('constants.TIME_TYPE.BREAK_IN')){
                $breakInList = $group->pluck('corrected_time')->toArray();
            }elseif($type == config('constants.TIME_TYPE.BREAK_OUT')){
                $breakOutList = $group->pluck('corrected_time')->toArray();
            }
        };

        $max = count($breakInList);
        for($i = 1; $i <= $max; $i++){
            $breakTimeLists[$i] = [
                'in' => self::formatTime($breakInList[$i - 1]),
                'out' => self::formatTime($breakOutList[$i - 1])
            ];
        }

        return [
            'clockTimeLists' => $clockTimeLists,
            'breakTimeLists' => $breakTimeLists
        ];
    }

    public static function getEffectiveAttendanceRecords($timecards,$targetUserId,$targetDate,$attendanceCorrectionId,$clockTimeLists,$breakTimeLists){
        $timecardData = $timecards->orderBy('time')
            ->get()
            ->groupBy('type');

        foreach($timecardData as  $type => $group){
            if(in_array($type,[config('constants.TIME_TYPE.CLOCK_IN'),config('constants.TIME_TYPE.CLOCK_OUT')])){
                $first = $group->first();
                $correction = AttendanceCorrectionDetail::where('timecard_id', $first->id)
                    ->first();

                $clockTimeLists[$type] = [
                    'time' => !is_null($correction) ? self::formatTime($correction->corrected_time) : $first->time
                ];
            }else{
                $breakTimeLists = self::getBreakTimes($targetUserId, $targetDate);

                // 修正申請時に新規で追加した休憩のペアも取得すること！
                $newBreakTimes = AttendanceCorrectionDetail::where('attendance_correction_id', $attendanceCorrectionId)
                    ->whereIn('type', [config('constants.TIME_TYPE.BREAK_IN'), config('constants.TIME_TYPE.BREAK_OUT')])
                    ->where('timecard_id', null)
                    ->get();

                foreach($newBreakTimes as $newTime){
                    if($newTime->type == config('constants.TIME_TYPE.BREAK_IN')){
                        $breakTimeLists[count($breakTimeLists)+1]['in'] = self::formatTime($newTime->corrected_time);
                    }elseif($newTime->type == config('constants.TIME_TYPE.BREAK_OUT')){
                        $breakTimeLists[count($breakTimeLists)]['out'] = self::formatTime($newTime->corrected_time);
                    }
                }
            }
        }

        return [
            'clockTimeLists' => $clockTimeLists,
            'breakTimeLists' => $breakTimeLists
        ];
    }

    public static function getBreakTimes($useId,$selectedDate){//休憩時間を取得する
        $breakTimes = Timecard::breakTimeData($useId, $selectedDate)
            ->orderBy('time')
            ->get();

        $breakTimeLists = [];
        $breakQueue = [];
        $index = 1;

        if($breakTimes->isEmpty()){
            return [];
        }else{
            foreach($breakTimes as $breakTime){
                if($breakTime->type == config('constants.TIME_TYPE.BREAK_IN')){
                    $breakQueue[] = substr($breakTime->time,self::START_INDEX,self::TIME_MAX_LENGTH);
                }elseif($breakTime->type == config('constants.TIME_TYPE.BREAK_OUT') && !empty($breakQueue)){
                    $start = array_shift($breakQueue);
                    $end = substr($breakTime->time,self::START_INDEX,self::TIME_MAX_LENGTH);

                    $breakTimeLists[$index] = [
                        'in' => $start,
                        'out' => $end,
                    ];

                    $index++;
                }
            }
        }

        return $breakTimeLists;
    }
}
