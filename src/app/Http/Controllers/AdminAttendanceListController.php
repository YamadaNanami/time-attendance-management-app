<?php

namespace App\Http\Controllers;

use App\Helper\AttendanceHelper;
use App\Models\Timecard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminAttendanceListController extends Controller
{
    const START_INDEX = 0;
    const TIME_MAX_LENGTH = 5;

    public function index(Request $request){
        $calcDay = $request->input('dayBtn');

        if($calcDay){
            // セッションから画面に表示されていた年月を取得する
            $selectedDay = Carbon::parse(session('selectedDay'));

            $selectedDay = match ($calcDay) {
                'subDay' => $selectedDay->subDay(),
                'addDay' => $selectedDay->addDay(),
                default => $selectedDay,
            };

        }else{ // 初期表示時の日付設定
            $selectedDay = Carbon::now();
        }

        // セッションに表示年月日を保存する
        session()->put('selectedDay', $selectedDay->format('Y-m-d'));

        // DB検索用に日付のフォーマットを変換する
        $date = $selectedDay->format('Y-m-d');

        $userList = User::where('role', 1)
            ->orderBy('id')
            ->get();

        // 勤怠一覧データ用の配列作成
        $data = [];

        foreach($userList as $user){
            $workDay = $user->workDays()
                ->where('date',$date)
                ->first();

            $data[] = $this->generateTimecardRow($workDay,$user, $date);
        }

        // 画面表示用に日付のフォーマットを変換する
        $year = $selectedDay->year;
        $month = $selectedDay->month;
        $day = $selectedDay->day;

        $displayDateJa = $year.'年'.$month.'月'.$day.'日';

        return view('admin/attendance_list',compact('displayDateJa','selectedDay','data'));
    }

    private function generateTimecardRow($workDay,$user,$date){ // 対象日の勤怠情報を生成する

        $clockIn = $workDay
            ? $workDay->clock_in
            : Timecard::timecardData($user->id, $date, config('constants.TIME_TYPE.CLOCK_IN'))->first()?->time ?? '';

        $clockOut = $workDay ? $workDay->clock_out : '';

        $breakTime = $workDay
            ? AttendanceHelper::formatTime($workDay->break_duration)
            : AttendanceHelper::formatTime(Timecard::breakTimeData($user->id, $date)->exists()
                ? AttendanceHelper::calcBreakTime(Timecard::breakTimeData($user->id, $date))
                : 0);

        $totalWorkTime = $workDay
            ? AttendanceHelper::formatTime($workDay->total_work_time)
            : '';

        $id = $workDay->id ?? 0;

        return [
            'id' => $id,
            'clockIn' => substr($clockIn,self::START_INDEX,self::TIME_MAX_LENGTH),
            'clockOut' => substr($clockOut,self::START_INDEX,self::TIME_MAX_LENGTH),
            'breakTime' => $breakTime,
            'totalWorkTime' => $totalWorkTime,
            'name' => $user->name,
            'userId' =>$user->id
        ];

    }
}
