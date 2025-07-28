<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    public $fillable = [
        'user_id',
        'target_date',
        'comment',
        'approval_flag'
    ];

    public function details(){
        return $this->hasMany(AttendanceCorrectionDetail::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
