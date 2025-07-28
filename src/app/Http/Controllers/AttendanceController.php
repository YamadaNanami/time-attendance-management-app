<?php

namespace App\Http\Controllers;

use App\Helper\AttendanceHelper;
use App\Models\Timecard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index(){
        $userId = Auth::id();
        $today = Carbon::now()->format('Y-m-d');

        // ログインユーザーが最後に打刻したレコードを取得する
        $lastTimecard = Timecard::orderBy('time', 'desc')
            ->where('user_id',$userId)
            ->where('date',$today)
            ->first();

        // 適切なステータスを設定する
        if($lastTimecard == null){
            $status = '勤務外';
        }else{
            // 最終打刻(種類)から現在表示するステータスを取得する
            $type = $lastTimecard->type;
            switch($type){
                case config('constants.TIME_TYPE.CLOCK_IN'): //出勤
                    $status = '出勤中';
                    break;
                case config('constants.TIME_TYPE.BREAK_IN'): //休憩入
                    $status = '休憩中';
                    break;
                case config('constants.TIME_TYPE.BREAK_OUT'): //休憩戻
                    $status = '出勤中';
                    break;
                case config('constants.TIME_TYPE.CLOCK_OUT'): //退勤
                    $status = '退勤済';
                    break;
            }
        }

        return view('user/index',compact('status'));
    }

    public function createTimecard(Request $request){
        try{
            $userId = Auth::id();
            $date = Carbon::now()->format('Y-m-d');
            $time = Carbon::now()->format('H:i');
            $type = $request->input('type');

            // timecardsテーブルに登録するデータを成形する
            $timeCardData = [
                'user_id' => $userId,
                'date' => $date,
                'time' => $time,
                'type' => $type
            ];

            DB::transaction(function () use($type,$userId,$date,$time,$timeCardData) {
                if($type == config('constants.TIME_TYPE.CLOCK_OUT')){
                    //退勤時にwork_daysテーブルに1日の勤怠を登録する
                    AttendanceHelper::updateOrCreateWorkDayRow($userId, $date,$time);
                }

                // 打刻情報を登録する
                Timecard::create($timeCardData);
            });

            return redirect()->route('user.index');

        }catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back();
        }
    }
}
