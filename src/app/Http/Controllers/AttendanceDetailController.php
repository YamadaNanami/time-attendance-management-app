<?php

namespace App\Http\Controllers;

use App\Helper\AttendanceDetailHelper;
use App\Models\AttendanceCorrection;
use App\Models\Timecard;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceDetailController extends Controller
{
    public function index(Request $request,$id){
        if($request->routeIs('detail')){
            return $this->showAttendanceDetail($request,$id);
        }

        if($request->routeIs('admin.approval')){
            return $this->showApprovalDetail($id);
        }

        abort(404);
    }

    private function showAttendanceDetail($request,$id){
        if(Auth::user()->role == config('constants.ROLE.USER')){
            $user = Auth::user();
        }elseif(Auth::user()->role == config('constants.ROLE.ADMIN')){
            $user = User::find($request->userId);
        }

        $workDaysId = $id;
        $selectedDate = Carbon::create($request->selectedDate ?? session('selectedDay'));

        $attendanceCorrection = AttendanceCorrection::where('user_id', $user->id)
            ->where('target_date', $selectedDate)
            ->latest()
            ->first() ?? null;

        if(!is_null($attendanceCorrection) && $attendanceCorrection->approval_flag == config('constants.APPROVAL_FLAG.UNAPPROVED')){
            // 出勤・退勤・休憩時間の修正申請がある場合は申請中の勤怠情報も取得する
            $data = AttendanceDetailHelper::getAttendanceData($attendanceCorrection);

            $clockIn = $data['clockIn'];
            $clockOut = $data['clockOut'];
            $breakTimes = $data['breakTimeLists'];
        }else{
            if($workDaysId != 0){
                // 対象日の勤怠情報を取得する
                $workDays = WorkDay::workDay($user->id,$selectedDate)->first();

                //出勤時間を取得する
                $clockIn = $workDays->clock_in;

                //退勤時間を取得する
                $clockOut = $workDays->clock_out;
            }else{
                //出勤時間を取得する
                $clockIn = Timecard::timecardData($user->id, $selectedDate, config('constants.TIME_TYPE.CLOCK_IN'))
                    ->first()?->time ?? '';

                //退勤時間を取得する
                $clockOut = Timecard::timecardData($user->id, $selectedDate, config('constants.TIME_TYPE.CLOCK_OUT'))
                    ->first()?->time ?? '';
            }

            //休憩時間を取得する
            $breakTimes = AttendanceDetailHelper::getBreakTimes($user->id, $selectedDate);
        }

        $data = [
            'name' => $user->name,
            'id' => $workDaysId ?? 0,
            'selectedDate' => $selectedDate,
            'year' => $selectedDate->year,
            'month' => $selectedDate->month,
            'day' => $selectedDate->day,
            'clockIn' => AttendanceDetailHelper::formatTime($clockIn),
            'clockOut' => AttendanceDetailHelper::formatTime($clockOut),
            'breakTimeLists' => $breakTimes,
            'comment' => $attendanceCorrection['comment'] ?? null,
            'approvalFlag' => $attendanceCorrection['approval_flag'] ?? null,
            'attendanceCorrectionId' => $attendanceCorrection['id'] ?? null
        ];

        return view('shared.detail',compact('data'));
    }

    private function showApprovalDetail($id){
        $attendanceCorrection = AttendanceCorrection::find($id);

        if($attendanceCorrection->approval_flag == config('constants.APPROVAL_FLAG.UNAPPROVED')){
            // 未承認の場合
            $data = $this->createUnapprovedData($attendanceCorrection,$id);
        }elseif($attendanceCorrection->approval_flag == config('constants.APPROVAL_FLAG.APPROVED')){
            // 承認済みの場合
            $data = $this->createApprovedData($id,$attendanceCorrection);
        }

        if(Auth::user()->role == config('constants.ROLE.USER')){
            return view('shared.detail', compact('data'));
        }elseif(Auth::user()->role == config('constants.ROLE.ADMIN')){
            return view('admin.approval',compact('data'));
        }
    }

    private function createUnapprovedData($attendanceCorrection,$id){
        $formData = AttendanceDetailHelper::getAttendanceData($attendanceCorrection);

        $fullDate = Carbon::createFromFormat('Y-m-d',$formData['targetDate']);

        $month = ltrim($fullDate->month, '0');
        $day = ltrim($fullDate->day, '0');

        return [
            'attendanceCorrectionId' => $id,
            'name' => $formData['name'],
            'year' => $fullDate->year,
            'month' => $month,
            'day' => $day,
            'clockIn' => $formData['clockIn'],
            'clockOut' => $formData['clockOut'],
            'breakTimeLists' => $formData['breakTimeLists'],
            'comment' => $formData['comment'],
            'approvalFlag' => $formData['approvalFlag']
        ];
    }

    private function createApprovedData($id,$attendanceCorrection){
        $user = $attendanceCorrection->user;
        $userId = $user->id;
        $targetDate = $attendanceCorrection->target_date;

        $workDay = WorkDay::workDay($userId,$targetDate)->first();

        $clockIn =  $workDay->clock_in;

        $clockOut = $workDay->clock_out;

        //休憩時間を取得する
        $breakTimes = AttendanceDetailHelper::getBreakTimes($userId, $targetDate);

        $fullDate = Carbon::createFromFormat('Y-m-d',$targetDate);

        $month = ltrim($fullDate->month, '0');
        $day = ltrim($fullDate->day, '0');

        return [
            'attendanceCorrectionId' => $id,
            'name' => $user->name,
            'year' => $fullDate->year,
            'month' => $month,
            'day' => $day,
            'clockIn' => $clockIn,
            'clockOut' => $clockOut,
            'breakTimeLists' => $breakTimes,
            'comment' => $attendanceCorrection->comment,
            'approvalFlag' => $attendanceCorrection->approval_flag
        ];
    }

}
