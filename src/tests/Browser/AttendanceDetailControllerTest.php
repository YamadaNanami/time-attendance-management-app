<?php

namespace Tests\Browser;

use App\Models\Timecard;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AttendanceDetailControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * ID:10
     */
    public function test_check_name(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now();

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        $workDay = WorkDay::create([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
            'clock_in' => '8:00:00',
            'clock_out' => '17:00:00',
            'break_duration' => 60,
            'total_work_time' => 480
        ]);

        $this->browse(function (Browser $browser) use ($user, $workDay, $date) {
            $browser->loginAs($user)
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString());

            $nameRowText = $browser->element('.detail-table tbody .table-row:nth-of-type(1) .table-detail:nth-of-type(1)')->getText(); //名前行のtdを取得
            $this->assertEquals($user->name, $nameRowText); //ログインユーザー名と一致するか確認
        });
    }

    public function test_check_date(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now();

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        $workDay = WorkDay::create([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
            'clock_in' => '8:00:00',
            'clock_out' => '17:00:00',
            'break_duration' => 60,
            'total_work_time' => 480
        ]);

        $this->browse(function (Browser $browser) use ($user, $workDay, $date) {
            $browser->loginAs($user)
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString());

            $yearRowText = $browser->element('.detail-table tbody .table-row:nth-of-type(2) .table-detail:nth-of-type(1) .year')->getText(); //日付行の年を取得
            $this->assertEquals($date->format('Y年'), $yearRowText); //選択した年と一致するか確認

            $dateRowText = $browser->element('.detail-table tbody .table-row:nth-of-type(2) .table-detail:nth-of-type(1) p:nth-of-type(2)')->getText(); //日付行の月日を取得
            $this->assertEquals($date->format('n月j日'), $dateRowText); //選択した月日と一致するか確認
        });
    }

    public function test_check_clock_time(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now();

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        $workDay = WorkDay::create([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
            'clock_in' => '8:00:00',
            'clock_out' => '17:00:00',
            'break_duration' => 0,
            'total_work_time' => 480
        ]);

        $this->browse(function (Browser $browser) use ($user, $workDay, $date) {
            $browser->loginAs($user)
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString());

            $inTime = $browser->value('.detail-table tbody .table-row:nth-of-type(3) .table-detail input:nth-of-type(1)'); //出勤・退勤行の出勤時間を取得
            $this->assertEquals('08:00', $inTime); //打刻と一致するか確認

            $outTime = $browser->value('.detail-table tbody .table-row:nth-of-type(3) .table-detail input:nth-of-type(2)'); //出勤・退勤行の退勤時間を取得
            $this->assertEquals('17:00', $outTime); //打刻と一致するか確認
        });
    }

    public function test_check_break_time(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now();

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        $workDay = WorkDay::create([
            'user_id' => $user->id,
            'date' => $date->toDateString(),
            'clock_in' => '8:00:00',
            'clock_out' => '17:00:00',
            'break_duration' => 60,
            'total_work_time' => 480
        ]);

        $this->browse(function (Browser $browser) use ($user, $workDay, $date) {
            $browser->loginAs($user)
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString());

            $inTime = $browser->value('.detail-table tbody .table-row:nth-of-type(4) .table-detail input:nth-of-type(1)'); //休憩行の休憩入時間を取得
            $this->assertEquals('12:00', $inTime); //打刻と一致するか確認

            $outTime = $browser->value('.detail-table tbody .table-row:nth-of-type(4) .table-detail input:nth-of-type(2)'); //休憩行の休憩戻時間を取得
            $this->assertEquals('13:00', $outTime); //打刻と一致するか確認
        });
    }
}
