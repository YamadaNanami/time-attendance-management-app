<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkDaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'user_id' => 1,
            'date' => '2025-07-01',
            'clock_in' => '8:00:00',
            'clock_out' => '17:00:00',
            'break_duration' => 60,
            'total_work_time' => 480
        ];
        DB::table('work_days')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-07-01',
            'clock_in' => '8:00:00',
            'clock_out' => '21:00:00',
            'break_duration' => 120,
            'total_work_time' => 660
        ];
        DB::table('work_days')->insert($param);
    }
}
