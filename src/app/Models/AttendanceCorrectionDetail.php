<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionDetail extends Model
{
    protected $fillable = [
        'attendance_correction_id',
        'timecard_id',
        'type',
        'corrected_time'
    ];

    public function correction(){
        return $this->belongsTo(AttendanceCorrection::class);
    }

    public function timecard(){
        return $this->belongsTo(Timecard::class);
    }
}
