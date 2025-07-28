<?php

namespace App\Http\Controllers;

use App\Helper\AttendanceCorrectionHelper;
use App\Helper\AttendanceHelper;
use App\Http\Requests\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrection;
use App\Models\Timecard;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetailController extends Controller
{
    const START_INDEX = 0;
    const REPLACE_LENGTH = 1;
    const TIME_MAX_LENGTH = 5;
    const DATE_MAX_LENGTH = 10;

    public function createAttendanceCorrection(AttendanceCorrectionRequest $request,$id){
        $userId = $request->userId ?? Auth::id();
        $role = Auth::user()->role;
        $selectedDate = Carbon::createFromDate($request->selectedDate)->format('Y-m-d');

        $clockIn = $request->clockIn;
        $clockOut = $request->clockOut;
        $breakIn = $request->breakIn;
        $breakOut = $request->breakOut;

        try{
            DB::beginTransaction(); //トランザクション開始

            if(AttendanceCorrectionHelper::checkBreakPair($breakIn,$breakOut)){
                // 休憩入または休憩戻のペアで一方しか入力されていない場合
                DB::rollBack();
                return redirect()->back()->withInput();
            }

            $approvalFlag = $role == config('constants.ROLE.USER')
                ? config('constants.APPROVAL_FLAG.UNAPPROVED')
                : config('constants.APPROVAL_FLAG.NO_APPROVAL_NEEDED');

            $attendanceCorrection = AttendanceCorrection::create([
                'user_id' => $userId,
                'target_date' => $selectedDate,
                'comment' => $request->comment,
                'approval_flag' => $approvalFlag
            ]);

            if ($role == config('constants.ROLE.USER')) {
                $this->handleUserRequest($attendanceCorrection->id, $id, $userId, $selectedDate, $clockIn, $clockOut, $breakIn, $breakOut);
            } else {
                $this->handleAdminRequest($id, $userId, $selectedDate, $clockIn, $clockOut, $breakIn, $breakOut);
            }

            DB::commit(); // DBへコミット

            $workDaysId = WorkDay::where('user_id', $userId)
                ->where('date', $selectedDate)
                ->first(['id']);

            return redirect()->route('detail',[
                'id' => Arr::get($workDaysId,'id') ?? 0,
                'selectedDate' => $selectedDate,
                'userId' => $userId
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    private function handleUserRequest($correctionId, $workDayId, $userId, $date, $clockIn, $clockOut, $breakIn, $breakOut){
        if($workDayId = 0){
            // 新規申請
            AttendanceCorrectionHelper::createDetailRows($correctionId,$clockIn,$clockOut,$breakIn,$breakOut);
        }else{
            // 修正申請
            $clockDiffs = AttendanceCorrectionHelper::compareClockTimes($userId, $date,$clockIn,$clockOut);
            $breakDiffs = AttendanceCorrectionHelper::compareBreakTimes($userId, $date, $breakIn, $breakOut);

            if(!empty($clockDiffs)){
                AttendanceCorrectionHelper::createTimeLogUpdateRows($correctionId, $clockDiffs);
            }

            if(!empty($breakDiffs)){
                AttendanceCorrectionHelper::createTimeLogUpdateRows($correctionId, $breakDiffs);
            }
        }
    }

    private function handleAdminRequest($workDayId, $userId, $date, $clockIn, $clockOut, $breakIn, $breakOut){
        if($workDayId == 0){
            // timecardsテーブルへの登録処理
            AttendanceCorrectionHelper::createTimecardRows($userId,$date,$clockIn,$clockOut,$breakIn,$breakOut);
        } else {
            // 修正申請
            $clockDiffs = AttendanceCorrectionHelper::compareClockTimes($userId, $date, $clockIn, $clockOut);
            $breakDiffs = AttendanceCorrectionHelper::compareBreakTimes($userId, $date, $breakIn, $breakOut);

            // ログインユーザーが管理者の場合（修正を直接反映させる）
            foreach($clockDiffs as $diff){
                Timecard::find($diff['timecard_id'])->update([
                    'time' => $diff['corrected_time']
                ]);
            }

            foreach($breakDiffs as $diff){
                if(array_key_exists('timecard_id',$diff)){
                    Timecard::find($diff['timecard_id'])->update([
                        'time' => $diff['corrected_time']
                    ]);
                }else{
                    Timecard::create([
                        'user_id' => $userId,
                        'date' => $date,
                        'time' => $diff['corrected_time'],
                        'type' => $diff['type']
                    ]);
                }
            }
        }

        $outTime = Timecard::where('user_id', $userId)
            ->where('date', $date)
            ->where('type', config('constants.TIME_TYPE.CLOCK_OUT'))
            ->first();

        // work_daysテーブルへの登録処理
        AttendanceHelper::updateOrCreateWorkDayRow($userId, $date, $outTime->time);
    }

}
