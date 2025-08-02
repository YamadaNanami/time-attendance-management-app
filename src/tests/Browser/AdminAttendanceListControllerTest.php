<?php

namespace Tests\Browser;

use App\Models\Timecard;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminAttendanceListControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * ID:12
     */
    public function test_confirm_attendance_list_for_admin(): void
    {
        $user1 = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);
        $user2 = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now();

        Timecard::insert([
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ],[
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        WorkDay::insert([
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 0,
                'total_work_time' => 480
            ],[
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 60,
                'total_work_time' => 420
            ]
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        $attendanceRow1 = '<tr class="table-row">
                    <td class="table-detail">'.$user1->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail">17:00</td>
                    <td class="table-detail"></td>
                    <td class="table-detail">8:00</td>';
        $attendanceRow2 = '<tr class="table-row">
                    <td class="table-detail">'.$user2->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail">17:00</td>
                    <td class="table-detail">1:00</td>
                    <td class="table-detail">7:00</td>';

        $this->browse(function (Browser $browser) use ($date, $admin, $attendanceRow1, $attendanceRow2) {
            $browser->loginAs($admin)
                ->visit('/admin/attendance/list')
                ->waitFor('.selected-date')
                ->assertSeeIn('.selected-date', $date->format('Y/m/d')) //画面遷移時に現在日が表示されているか確認
                ->waitFor('.list-table')
                ->assertSourceHas($attendanceRow1) //対象日に登録された勤怠情報の表示確認
                ->assertSourceHas($attendanceRow2); //対象日に登録された勤怠情報の表示確認
        });

    }

    public function test_show_attendance_list_at_sub_day(){
        $user1 = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);
        $user2 = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now()->subDay();

        Timecard::insert([
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ],[
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        WorkDay::insert([
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 0,
                'total_work_time' => 480
            ],[
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 60,
                'total_work_time' => 420
            ]
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        $attendanceRow1 = '<tr class="table-row">
                    <td class="table-detail">'.$user1->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail">17:00</td>
                    <td class="table-detail"></td>
                    <td class="table-detail">8:00</td>';
        $attendanceRow2 = '<tr class="table-row">
                    <td class="table-detail">'.$user2->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail">17:00</td>
                    <td class="table-detail">1:00</td>
                    <td class="table-detail">7:00</td>';

        $this->browse(function (Browser $browser) use ($date, $admin, $attendanceRow1, $attendanceRow2) {
            $browser->loginAs($admin)
                ->visit('/admin/attendance/list')
                ->waitFor('.date-btn')
                ->press('前日') //前日ボタンを押下
                ->waitFor('.list-table')
                ->assertSourceHas($attendanceRow1) //対象日に登録された勤怠情報の表示確認
                ->assertSourceHas($attendanceRow2); //対象日に登録された勤怠情報の表示確認
        });
    }

    public function test_show_attendance_list_at_add_day(){
        $user1 = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);
        $user2 = User::factory()->create([
            'email' => 'test2@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $date = Carbon::now()->addDay();

        Timecard::insert([
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ],[
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ]
        ]);

        WorkDay::insert([
            [
                'user_id' => $user1->id,
                'date' => $date->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 0,
                'total_work_time' => 480
            ],[
                'user_id' => $user2->id,
                'date' => $date->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 60,
                'total_work_time' => 420
            ]
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        $attendanceRow1 = '<tr class="table-row">
                    <td class="table-detail">'.$user1->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail">17:00</td>
                    <td class="table-detail"></td>
                    <td class="table-detail">8:00</td>';
        $attendanceRow2 = '<tr class="table-row">
                    <td class="table-detail">'.$user2->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail">17:00</td>
                    <td class="table-detail">1:00</td>
                    <td class="table-detail">7:00</td>';

        $this->browse(function (Browser $browser) use ($date, $admin, $attendanceRow1, $attendanceRow2) {
            $browser->loginAs($admin)
                ->visit('/admin/attendance/list')
                ->waitFor('.date-btn')
                ->press('翌日') //翌日ボタンを押下
                ->waitFor('.list-table')
                ->assertSourceHas($attendanceRow1) //対象日に登録された勤怠情報の表示確認
                ->assertSourceHas($attendanceRow2); //対象日に登録された勤怠情報の表示確認
        });
    }
}
