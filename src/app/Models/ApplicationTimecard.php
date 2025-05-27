<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationTimecard extends Model
{
    protected $fillable = [
        'application_id',
        'timecard_id',
        'approval_flag'
    ];
}
