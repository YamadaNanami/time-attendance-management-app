<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationListController extends Controller
{
    public function index(Request $request){
        $approval_flag = $request->input('approval_flag') ?? 0;
        $role = Auth::user()->role;

        if($role == 1){
            $data = $this->userIndex($approval_flag);
        }elseif($role == 2){
            $data = $this->adminIndex($approval_flag);
        }

        return view('shared.application_list',compact('approval_flag','data'));
    }

    private function userIndex($approval_flag){
        $user = Auth::user();

        $lists = AttendanceCorrection::where('user_id',$user->id)
            ->where('approval_flag',$approval_flag)
            ->get();

        $data = $lists->map(function ($list) use ($user) {
            return [
                'name' => $user->name,
                'target_date' => Carbon::parse($list->target_date),
                'comment' => $list->comment,
                'corrected_date' => Carbon::parse($list->created_at),
                'attendanceCorrectionId' => $list->id
            ];
        });

        return $data;
    }

    private function adminIndex($approval_flag){
        $lists = AttendanceCorrection::where('approval_flag',$approval_flag)
            ->with('user')
            ->get();

        $data = $lists->map(function ($list) {
            return [
                'name' => $list->user->name,
                'target_date' => Carbon::parse($list->target_date),
                'comment' => $list->comment,
                'corrected_date' => Carbon::parse($list->created_at),
                'attendanceCorrectionId' => $list->id
            ];
        });

        return $data;
    }

}
