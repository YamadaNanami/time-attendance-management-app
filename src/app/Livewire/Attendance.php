<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class Attendance extends Component
{
    public $currentDate;
    public $currentTime;

    public function getCurrentDate(){
        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekdayNumber = Carbon::now()->format('w');
        $japaneseWeekday = $japaneseWeekdays[$weekdayNumber];

        $year = Carbon::now()->format('Y');
        $month = ltrim(Carbon::now()->format('m'),'0');
        $day = ltrim(Carbon::now()->format('d'),'0');

        $this->currentDate = $year.'年'.$month.'月'.$day.'日('.$japaneseWeekday.')';
    }

    public function getCurrentTime(){
        $this->currentTime = Carbon::now()->format('H:i');
    }

    public function refreshDate(){
        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekdayNumber = Carbon::now()->format('w');
        $japaneseWeekday = $japaneseWeekdays[$weekdayNumber];

        $year = Carbon::now()->format('Y');
        $month = ltrim(Carbon::now()->format('m'),'0');
        $day = ltrim(Carbon::now()->format('d'),'0');

        $this->currentDate = $year.'年'.$month.'月'.$day.'日('.$japaneseWeekday.')';
    }

    public function refreshTime(){
        $this->currentTime = Carbon::now()->format('H:i');
    }

    public function render()
    {
        return view('livewire.attendance');
    }
}
