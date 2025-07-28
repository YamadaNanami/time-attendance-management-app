<?php

namespace App\Http\Controllers;

use App\Helper\AttendanceHelper;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceCorrectionDetail;
use App\Models\Timecard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function storeAndUpdate($id){
        try{
            DB::beginTransaction(); //トランザクション開始

            // attendance_correctionsテーブルを更新する
            $attendanceCorrection  = AttendanceCorrection::find($id);
            $attendanceCorrection->update(['approval_flag' => 1]);

            // 修正申請内容を取得する
            $details = AttendanceCorrectionDetail::where('attendance_correction_id', $id)
                ->get();

            $userId = $attendanceCorrection->user_id;
            $date = $attendanceCorrection->target_date;

            foreach($details as $detail){
                if(is_null($detail['timecard_id'])){
                    // timecardsテーブルに新規登録する場合
                    Timecard::create([
                        'user_id' => $userId,
                        'date' => $date,
                        'type' => $detail['type'],
                        'time' => $detail['corrected_time'],
                    ]);
                }else{
                    // timecardsテーブルを更新する場合
                    Timecard::find($detail['timecard_id'])->update([
                        'time' => $detail['corrected_time']
                    ]);
                }
            }

            $clockOut = Timecard::timecardData($userId, $date, config('constants.TIME_TYPE.CLOCK_OUT'))
                ->first();

            //work_daysへの登録・更新処理
            AttendanceHelper::updateOrCreateWorkDayRow($userId,$date,$clockOut->time);

            DB::commit();

            return redirect()->route('admin.approval',['attendance_correct_request' => $id]);

        }catch(\Exception $e){
            DB::rollBack();
            Log::error('[ApprovalController]'.$e->getMessage());
            return redirect()->back();
        }
    }
}
