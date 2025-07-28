<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminStaffListController extends Controller
{
    public function index(){
        $userList = User::where('role', 1)
            ->orderBy('id')
            ->get();

        $staffList = [];

        foreach($userList as $user){
            array_push($staffList, [
                'name' => $user->name,
                'email' => $user->email,
                'id' => $user->id
            ]);
        }

        return view('admin.staff_list', compact('staffList'));
    }
}
