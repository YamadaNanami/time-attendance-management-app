<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkDay extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'break_duration',
        'total_work_time'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeWorkDay($query,$userId,$date){
        $query->where('user_id', $userId)
            ->where('date', $date);
    }
}
