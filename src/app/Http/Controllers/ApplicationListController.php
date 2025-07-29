<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrection;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationListController extends Controller
{
    public function index(Request $request){
        $approvalFlag = $request->input('approvalFlag') ?? 0;
        $role = Auth::user()->role;

        if($role == 1){
            $data = $this->userIndex($request,$approvalFlag);
        }elseif($role == 2){
            $data = $this->adminIndex($approvalFlag);
        }

        return view('shared.application_list',compact('approvalFlag','data'));
    }

    private function userIndex($request,$approvalFlag){
        $user = Auth::user();

        $lists = AttendanceCorrection::where('user_id',$user->id)
            ->where('approval_flag',$approvalFlag)
            ->get();

        $isListRoute = $request->routeIs('application_list');

        $data = $lists->map(function ($list) use ($user,$isListRoute) {
            $targetDate = Carbon::parse($list->target_date);

            $item =  [
                'name' => $user->name,
                'targetDate' => $targetDate,
                'comment' => $list->comment,
                'corrected_date' => Carbon::parse($list->created_at),
                'attendanceCorrectionId' => $list->id,
            ];

            if($isListRoute){
                $workDaysId = WorkDay::where('user_id', $user->id)
                    ->where('date', $targetDate)
                    ->value('id');

                $item['workDaysId'] = $workDaysId ?? 0;
            }

            return $item;

        });

        return $data;
    }

    private function adminIndex($approvalFlag){
        $lists = AttendanceCorrection::where('approval_flag',$approvalFlag)
            ->with('user')
            ->get();

        $data = $lists->map(function ($list) {
            return [
                'name' => $list->user->name,
                'targetDate' => Carbon::parse($list->target_date),
                'comment' => $list->comment,
                'corrected_date' => Carbon::parse($list->created_at),
                'attendanceCorrectionId' => $list->id
            ];
        });

        return $data;
    }

}
