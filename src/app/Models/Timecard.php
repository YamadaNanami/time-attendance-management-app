<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timecard extends Model
{
    protected $fillable = [
        'user_id',
        'datetime',
        'type'
    ];
}
