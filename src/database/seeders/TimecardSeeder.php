<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimecardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $param = [
            'user_id' => 1,
            'date' => '2025-05-25',
            'time' => '8:00:00',
            'type' => 1
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 1,
            'date' => '2025-05-25',
            'time' => '12:00:00',
            'type' => 2
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 1,
            'date' => '2025-05-25',
            'time' => '13:00:00',
            'type' => 3
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 1,
            'date' => '2025-05-25',
            'time' => '17:00:00',
            'type' => 4
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-05-25',
            'time' => '8:00:00',
            'type' => 1
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-05-25',
            'time' => '12:00:00',
            'type' => 2
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-05-25',
            'time' => '13:00:00',
            'type' => 3
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-05-25',
            'time' => '17:00:00',
            'type' => 2
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-05-25',
            'time' => '18:00:00',
            'type' => 3
        ];
        DB::table('timecards')->insert($param);

        $param = [
            'user_id' => 2,
            'date' => '2025-05-25',
            'time' => '21:00:00',
            'type' => 4
        ];
        DB::table('timecards')->insert($param);

    }
}
