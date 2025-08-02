<?php

namespace Tests\Browser;

use App\Models\Timecard;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DetailControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * ID:11
     */
    public function test_correction_fail_clock(){
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
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString())
                ->type('input[name="clockIn"]','18:00') //出勤時間に退勤時間より後の時刻を入力する
                ->press('修正')
                ->assertSeeIn('.detail-table tbody .table-row:nth-of-type(3) .table-detail .error-msg','出勤時間もしくは退勤時間が不適切な値です'); //エラーメッセージの確認
        });
    }

    public function test_correction_fail_break_in(){
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
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString())
                ->type('input[name="breakIn[1]"]','18:00') //休憩開始時間に退勤時間より後の時刻を入力する
                ->press('修正')
                ->assertSeeIn('.detail-table tbody .table-row:nth-of-type(4) .table-detail .error-msg','休憩時間が勤務時間外です'); //エラーメッセージの確認(エラーメッセージは機能要件に記載の文言を使用)
        });
    }

    public function test_correction_fail_break_out(){
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
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString())
                ->type('input[name="breakOut[1]"]','18:00') //休憩開始時間に退勤時間より後の時刻を入力する
                ->press('修正')
                ->assertSeeIn('.detail-table tbody .table-row:nth-of-type(4) .table-detail .error-msg','休憩時間が勤務時間外です'); //エラーメッセージの確認(エラーメッセージは機能要件に記載の文言を使用)
        });
    }

    public function test_correction_fail_comment_is_null(){
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
                ->visit('/attendance/' . $workDay->id . '?selectedDate=' . $date->toDateString())
                ->press('修正') //備考欄未入力の状態で修正ボタンを押下
                ->assertSeeIn('.detail-table tbody .table-row:last-of-type .table-detail .error-msg','備考を記入してください'); //エラーメッセージの確認
        });
    }
}
