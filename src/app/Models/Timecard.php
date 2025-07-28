<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timecard extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'type'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeTimecardData($query,$userId,$date,$type){
        $query->where('user_id',$userId)
                ->where('date', $date)
                ->where('type', $type);
    }

    public function scopeBreakTimeData($query,$userId,$date){
        $query->where('user_id',$userId)
                ->where('date', $date)
                ->whereIn('type', [config('constants.TIME_TYPE.BREAK_IN'),config('constants.TIME_TYPE.BREAK_OUT')]);
    }
}
