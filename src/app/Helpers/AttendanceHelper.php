<?php

namespace App\Helper;

use App\Models\Timecard;
use App\Models\WorkDay;
use Carbon\Carbon;

class AttendanceHelper{
    const MINUTES_LENGTH = 2;
    const TIME_MAX_LENGTH = 8;

    public static function convertWeekdays($startDate){ // 曜日を日本語に変換する
        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekdayNumber = $startDate->format('w');
        $japaneseWeekday = $japaneseWeekdays[$weekdayNumber];

        return $japaneseWeekday;
    }

    public static function formatTime($totalMinutes){ // 時間の表示形式を整える
        if ($totalMinutes <= 0)
            return '';

        $hours = floor($totalMinutes / 60);
        $minutes = str_pad($totalMinutes % 60, self::MINUTES_LENGTH, '0', STR_PAD_LEFT);

        return $hours . ':' . $minutes;
    }

    public static function updateOrCreateWorkDayRow($userId,$date,$time){ // work_daysへの登録処理
        $clockIn = Timecard::timecardData($userId,$date,config('constants.TIME_TYPE.CLOCK_IN'))->first()->time;

        // 休憩入と休憩戻のレコードを検索する
        $breakSql = Timecard::breakTimeData($userId,$date);

        // 休憩時間を算出する
        $breakDuration = $breakSql->exists() ? self::calcBreakTime($breakSql) : 0;

        // 退勤時間($time)をcalcWorkTimeで使用できるように修正する
        $clockOut = Carbon::parse($time)->format('H:i:s');

        // 合計時間を算出する
        $totalWorkTime = self::calcWorkTime($date, $clockIn, $clockOut, $breakDuration);

        WorkDay::updateOrCreate(
            ['user_id' => $userId,
        'date' => $date],
            [
                'user_id' => $userId,
                'date' => $date,
                'clock_in' => $clockIn,
                'clock_out' => $time,
                'break_duration' => $breakDuration,
                'total_work_time' => $totalWorkTime
            ]);

    }

    public static function calcBreakTime($sql){ // 休憩時間を算出する
        $breakTimes = $sql->orderBy('time')->get();
        $totalBreakTime = 0;

        $breakStartTimes = [];

        foreach($breakTimes as $breakTime){
            if($breakTime->type == config('constants.TIME_TYPE.BREAK_IN')){
                // 休憩入の場合
                $breakStartTimes[] = Carbon::parse($breakTime->time);
            }elseif($breakTime->type == config('constants.TIME_TYPE.BREAK_OUT') && !empty($breakStartTimes)){
                // 休憩戻かつ$breakStartTimesに値がある場合
                $breakStartTime = array_pop($breakStartTimes);
                $breakEndTime = Carbon::parse($breakTime->time);

                // 差分を$totalBreakTimeに加算
                $totalBreakTime += $breakStartTime->diffInMinutes($breakEndTime);
            }
        }

        return $totalBreakTime;
    }

    public static function calcWorkTime($date, $clockInTime, $clockOutTime, $totalBreakMinutes){ // 合計時間を算出する
        $in = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockInTime);
        $out = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockOutTime);

        $total = $in->diffInMinutes($out) - $totalBreakMinutes;

        return $total;
    }

}