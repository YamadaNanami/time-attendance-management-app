<?php

namespace App\Http\Controllers;

use App\Helper\AttendanceHelper;
use App\Models\Timecard;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceListController extends Controller
{
    const START_INDEX = 0;
    const MAX_MONTH_LENGTH = 2;
    const DATE_MAX_LENGTH = 10;
    const TIME_MAX_LENGTH = 5;

    public function index(Request $request){
        $userId = Auth::id();
        $calcMonth = $request->input('monthBtn');
        $data = $this->prepareMonthlyAttendanceData($userId, $calcMonth);

        $selectedYear = $data['selectedYear'];
        $selectedMonth = $data['selectedMonth'];
        $timecards = $data['timecards'];

        return view('shared/attendance_list', compact('selectedYear','selectedMonth','timecards'));
    }

    public function adminIndex(Request $request,int $id){
        $user = User::findOrFail($id);
        $calcMonth = $request->input('monthBtn');
        $data = $this->prepareMonthlyAttendanceData($id, $calcMonth);

        $selectedYear = $data['selectedYear'];
        $selectedMonth = $data['selectedMonth'];
        $timecards = $data['timecards'];

        return view('shared/attendance_list', compact('user','selectedYear','selectedMonth','timecards'));
    }

    public function exportCsv($id){
        $user = User::findOrFail($id);
        $timecards = $this->prepareMonthlyAttendanceData($id)['timecards'];
        $csvHeader = ['日付','出勤','退勤','休憩','合計'];

        $selectedDate = Carbon::parse(session('selectedDate'))->format('Y年m月');
        $filename = $user->name . '_' . $selectedDate . '分勤怠一覧' . '.csv';

        // csvに出力するデータの準備
        $csvData = [];
        foreach($timecards as $timecard){
            $temp = [
                $timecard['date'] . '('.$timecard['weekdayLabel'].')',
                $timecard['clockIn'],
                $timecard['clockOut'],
                $timecard['breakTime'],
                $timecard['total']
            ];
            array_push($csvData, $temp);
        }

        $response = new StreamedResponse(function () use ($csvData, $csvHeader) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);

        return $response;
    }

    private function prepareMonthlyAttendanceData(int $userId,$calcMonth = null): array{
        if($calcMonth){
            // セッションから画面に表示されていた年月を取得する
            $selectedDate = Carbon::parse(session('selectedDate'));

            $startDate = match ($calcMonth) {
                'subMonth' => $selectedDate->subMonth(),
                'addMonth' => $selectedDate->addMonth(),
                default => $selectedDate
            };

            $selectedYear = $selectedDate->year;
            $selectedMonth = $selectedDate->month;

        }else{ // 初期表示時の日付設定
            $startDate = Carbon::parse(session('selectedDate'))->startOfMonth()
                ?? Carbon::now()->startOfMonth();
        }

        $selectedYear = $startDate->year;
        $selectedMonth = str_pad($startDate->month,self::MAX_MONTH_LENGTH,'0',STR_PAD_LEFT);

        // セッションに表示年月を保存する
        session()->put('selectedDate', $selectedYear . '-' . $selectedMonth);

        $endDate = $startDate->copy()->endOfMonth();

        $timecards = [];

        // 選択されている月の勤怠情報を全て取得する
        while($startDate->lte($endDate)){
            $timecards[] = $this->generateTimecardRow($userId, $startDate);
            $startDate->addDay();
        }

        return [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'timecards' => $timecards,
        ];
    }

    private function generateTimecardRow($userId,$startDate){ // 対象日の勤怠情報を生成する

        // 対象の日付の曜日を日本語に変換する
        $weekdayLabel = AttendanceHelper::convertWeekdays($startDate);

        // 日付をDB検索用に整える
        $date = substr($startDate,self::START_INDEX,self::DATE_MAX_LENGTH);

        // 対象日の勤怠情報を取得する
        $workDay = WorkDay::workDay($userId,$date)->first() ?? false;

        $clockIn = $workDay
            ? $workDay->clock_in
            : Timecard::timecardData($userId, $date, config('constants.TIME_TYPE.CLOCK_IN'))->first()?->time ?? '';

        $clockOut = $workDay ? $workDay->clock_out : '';

        $breakTime = $workDay
            ? AttendanceHelper::formatTime($workDay->break_duration)
            : AttendanceHelper::formatTime(Timecard::breakTimeData($userId, $date)->exists()
                ? AttendanceHelper::calcBreakTime(Timecard::breakTimeData($userId, $date))
                : 0);

        $totalWorkTime = $workDay
            ? AttendanceHelper::formatTime($workDay->total_work_time)
            : '';

        $id = $workDay ? $workDay->id : 0;

        return [
            'id' => $id,
            'fullDate' => $startDate->format('Y-m-d'),
            'date' => $startDate->format('m/d'),
            'weekdayLabel' => $weekdayLabel,
            'clockIn' => substr($clockIn,self::START_INDEX,self::TIME_MAX_LENGTH),
            'clockOut' => substr($clockOut,self::START_INDEX,self::TIME_MAX_LENGTH),
            'breakTime' => $breakTime,
            'total' => $totalWorkTime,
        ];

    }
}
