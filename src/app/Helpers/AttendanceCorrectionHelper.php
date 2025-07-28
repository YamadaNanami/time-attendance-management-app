<?php

namespace App\Helper;

use App\Models\AttendanceCorrectionDetail;
use App\Models\Timecard;
use Carbon\Carbon;

class AttendanceCorrectionHelper{

    public static function checkBreakPair(array $breakIn, array $breakOut): bool{
        foreach($breakIn as $i => $inTime){
            if(is_null($inTime) xor is_null($breakOut[$i] ?? null)){
                return true;
            }
        }

        return false;
    }

    public static function createDetailRows($attendanceCorrectionId,$clockIn,$clockOut,$breakIn,$breakOut){

        $data = [
            [
                'attendance_correction_id' => $attendanceCorrectionId,
                'type' => config('constants.TIME_TYPE.CLOCK_IN'),
                'corrected_time' => $clockIn
            ],
            [
                'attendance_correction_id' => $attendanceCorrectionId,
                'type' => config('constants.TIME_TYPE.CLOCK_OUT'),
                'corrected_time' => $clockOut
            ],
        ];

        if(!is_null($breakIn[1])){
            array_push($data,
                [
                    'attendance_correction_id' => $attendanceCorrectionId,
                    'type' => config('constants.TIME_TYPE.BREAK_IN'),
                    'corrected_time' => $breakIn[1]
                ],
                [
                    'attendance_correction_id' => $attendanceCorrectionId,
                    'type' => config('constants.TIME_TYPE.BREAK_OUT'),
                    'corrected_time' => $breakOut[1],
                ]
            );
        }

        AttendanceCorrectionDetail::insert($data);
    }

    public static function createTimecardRows($userId,$selectedDate,$clockIn,$clockOut,$breakIn,$breakOut){

        $data = [
            [
                'user_id' => $userId,
                'date' => $selectedDate,
                'type' => config('constants.TIME_TYPE.CLOCK_IN'),
                'time' => $clockIn
            ],
            [
                'user_id' => $userId,
                'date' => $selectedDate,
                'type' => config('constants.TIME_TYPE.CLOCK_OUT'),
                'time' => $clockOut
            ],
        ];

        if(!is_null($breakIn[1])){
            array_push($data,
                [
                    'user_id' => $userId,
                    'date' => $selectedDate,
                    'type' => config('constants.TIME_TYPE.BREAK_IN'),
                    'time' => $breakIn[1]
                ],
                [
                    'user_id' => $userId,
                    'date' => $selectedDate,
                    'type' => config('constants.TIME_TYPE.BREAK_OUT'),
                    'time' => $breakOut[1],
                ]
            );
        }

        Timecard::insert($data);
    }

    public static function parseTime($time,$format = 'H:i'){
        return Carbon::createFromFormat($format, $time);
    }

    public static function compareClockTimes($userId,$selectedDate,$clockIn,$clockOut){
        $timecards = Timecard::where('user_id', $userId)
            ->where('date', $selectedDate)
            ->whereIn('type',[config('constants.TIME_TYPE.CLOCK_IN'),config('constants.TIME_TYPE.CLOCK_OUT')])
            ->get()
            ->groupBy('type'); // 1:出勤, 4:退勤

        $changeClockRows = [];

        $newIn = self::parseTime($clockIn);
        $newOut = self::parseTime($clockOut);

        $oldIn = self::parseTime($timecards[config('constants.TIME_TYPE.CLOCK_IN')][0]->time, 'H:i:s');
        $oldOut = self::parseTime($timecards[config('constants.TIME_TYPE.CLOCK_OUT')][0]->time, 'H:i:s');

        // 登録済みの勤怠情報とフォームで入力された時刻を比較する
        if($oldIn != $newIn){
            $changeClockRows[] = [
                'type' => config('constants.TIME_TYPE.CLOCK_IN'),
                'corrected_time' => $newIn,
                'timecard_id' => $timecards[config('constants.TIME_TYPE.CLOCK_IN')][0]->id
            ];
        }
        if($oldOut != $newOut){
            $changeClockRows[] = [
                'type' => config('constants.TIME_TYPE.CLOCK_OUT'),
                'corrected_time' => $newOut,
                'timecard_id' => $timecards[config('constants.TIME_TYPE.CLOCK_OUT')][0]->id
            ];
        }

        return $changeClockRows;
    }

    public static function compareBreakTimes($userId, $selectedDate,$breakIn,$breakOut){
        $oldBreakTimes = Timecard::breakTimeData($userId, $selectedDate)
            ->orderBy('time')
            ->get()
            ->groupBy('type'); // 2:休憩入, 3:休憩戻

        $changeBreakRows = [];

        $oldInList = $oldBreakTimes[config('constants.TIME_TYPE.BREAK_IN')];
        $oldOutList = $oldBreakTimes[config('constants.TIME_TYPE.BREAK_OUT')];

        for ($index = 1; $index <= count($oldInList); $index++){
            $newIn = self::parseTime($breakIn[$index] ?? '00:00');
            $newOut = self::parseTime($breakOut[$index] ?? '00:00');

            $oldIn =self::parseTime( $oldInList[$index - 1]->time,'H:i:s');
            $oldOut = self::parseTime($oldOutList[$index - 1]->time,'H:i:s');

            // 入力された時刻と登録済みの時刻を比較し、差分があれば$changeBreakRowsに追加する
            if($oldIn != $newIn){
                $changeBreakRows[] = [
                    'type' => config('constants.TIME_TYPE.BREAK_IN'),
                    'corrected_time' => $newIn,
                    'timecard_id' => $oldBreakTimes[config('constants.TIME_TYPE.BREAK_IN')][$index - 1]->id
                ];
            }
            if($oldOut != $newOut){
                $changeBreakRows[] = [
                    'type' => config('constants.TIME_TYPE.BREAK_OUT'),
                    'corrected_time' => $newOut,
                    'timecard_id' => $oldBreakTimes[config('constants.TIME_TYPE.BREAK_OUT')][$index - 1]->id
                ];
            }

        }

        $lastKey = array_key_last($breakIn);
        // 新規の休憩時間が入力された場合の処理
        if(!is_null($breakIn[$lastKey])){
            $changeBreakRows[] = [
                'type' => config('constants.TIME_TYPE.BREAK_IN'),
                'corrected_time' => $breakIn[$lastKey],
            ];
            $changeBreakRows[] = [
                'type' => config('constants.TIME_TYPE.BREAK_OUT'),
                'corrected_time' => $breakOut[$lastKey]
            ];
        }

        return $changeBreakRows;
    }

    public static function createTimeLogUpdateRows($attendanceCorrectionId,$changeRows){
        foreach($changeRows as $changeClock){
            AttendanceCorrectionDetail::create([
                'attendance_correction_id' =>$attendanceCorrectionId,
                'timecard_id' => $changeClock['timecard_id'] ?? null,
                'type' => $changeClock['type'],
                'corrected_time' => $changeClock['corrected_time']
            ]);
        }
    }

}