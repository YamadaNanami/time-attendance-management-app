<?php

namespace Tests\Browser;

use App\Models\Timecard;
use App\Models\User;
use App\Models\WorkDay;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AttendanceListControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * ID:9
     */
    public function test_confirm_attendance_list(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $startOfMonth = Carbon::now()->startOfMonth();
        $addTenDays = Carbon::now()->addDays(10);

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $startOfMonth->toDateString(),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user->id,
                'date' => $startOfMonth->toDateString(),
                'time' => '12:00:00',
                'type' => 2
            ],
            [
                'user_id' => $user->id,
                'date' => $startOfMonth->toDateString(),
                'time' => '13:00:00',
                'type' => 3
            ],
            [
                'user_id' => $user->id,
                'date' => $startOfMonth->toDateString(),
                'time' => '17:00:00',
                'type' => 4
            ],
            [
                'user_id' => $user->id,
                'date' => $addTenDays->toDateString(),
                'time' => '12:00:00',
                'type' => 1
            ],[
                'user_id' => $user->id,
                'date' => $addTenDays->toDateString(),
                'time' => '21:00:00',
                'type' => 4
            ]
        ]);

        WorkDay::insert([
            [
                'user_id' => $user->id,
                'date' => $startOfMonth->toDateString(),
                'clock_in' => '8:00:00',
                'clock_out' => '17:00:00',
                'break_duration' => 60,
                'total_work_time' => 480
            ],[
                'user_id' => $user->id,
                'date' => $addTenDays->toDateString(),
                'clock_in' => '12:00:00',
                'clock_out' => '21:00:00',
                'break_duration' => 0,
                'total_work_time' => 480
            ]
        ]);

        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];

        $startOfMonthRow = '<td class="table-detail">' . $startOfMonth->format('m/d(' . $japaneseWeekdays[$startOfMonth->format('w')] . ')') . '</td>
                <td class="table-detail">08:00</td>
                <td class="table-detail">17:00</td>
                <td class="table-detail">1:00</td>
                <td class="table-detail">8:00</td>';

        $addTenDaysRow = '<td class="table-detail">'.$addTenDays->format('m/d('.$japaneseWeekdays[$addTenDays->format('w')].')').'</td>
                <td class="table-detail">12:00</td>
                <td class="table-detail">21:00</td>
                <td class="table-detail"></td>
                <td class="table-detail">8:00</td>';

        $this->browse(function(Browser $browser) use ($user,$startOfMonthRow,$addTenDaysRow){
            $browser->loginAs($user)
                ->visit('/attendance/list')
                ->assertSourceHas($startOfMonthRow) //勤怠情報の確認
                ->assertSourceHas($addTenDaysRow); //勤怠情報の確認
        });
    }

    public function test_show_this_month(){
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $thisMonth = Carbon::now()->format('Y/m');

        $this->browse(function (Browser $browser) use ($user,$thisMonth) {
            $browser->loginAs($user)
                ->visit('/attendance/list')
                ->assertSeeIn('.selected-month', $thisMonth); //表示年月の確認
        });
    }

    public function test_show_sub_month_data(){
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $subMonth = Carbon::now()->subMonth()->startOfMonth();

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $subMonth->toDateString(),
                'time' => '12:00:00',
                'type' => 1
            ],[
                'user_id' => $user->id,
                'date' => $subMonth->toDateString(),
                'time' => '21:00:00',
                'type' => 4
            ]
        ]);

        WorkDay::create([
            'user_id' => $user->id,
            'date' => $subMonth->toDateString(),
            'clock_in' => '12:00:00',
            'clock_out' => '21:00:00',
            'break_duration' => 0,
            'total_work_time' => 480
        ]);

        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];

        $code = '<td class="table-detail">'.$subMonth->format('m/d('.$japaneseWeekdays[$subMonth->format('w')].')').'</td>
                <td class="table-detail">12:00</td>
                <td class="table-detail">21:00</td>
                <td class="table-detail"></td>
                <td class="table-detail">8:00</td>';

        $this->browse(function (Browser $browser) use ($user, $subMonth,$code) {
            $browser->loginAs($user)
                ->visit('/attendance/list')
                ->press('前月') //前月ボタンを押下
                ->waitFor('.selected-month')
                ->assertSeeIn('.selected-month', $subMonth->format('Y/m')) //表示年月の確認
                ->assertSourceHas($code); //前月の情報の表示確認
        });
    }

    public function test_show_add_month_data(){
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $addMonth = Carbon::now()->addMonth()->startOfMonth();

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => $addMonth->toDateString(),
                'time' => '12:00:00',
                'type' => 1
            ],[
                'user_id' => $user->id,
                'date' => $addMonth->toDateString(),
                'time' => '21:00:00',
                'type' => 4
            ]
        ]);

        WorkDay::create([
            'user_id' => $user->id,
            'date' => $addMonth->toDateString(),
            'clock_in' => '12:00:00',
            'clock_out' => '21:00:00',
            'break_duration' => 0,
            'total_work_time' => 480
        ]);

        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];

        $code = '<td class="table-detail">'.$addMonth->format('m/d('.$japaneseWeekdays[$addMonth->format('w')].')').'</td>
                <td class="table-detail">12:00</td>
                <td class="table-detail">21:00</td>
                <td class="table-detail"></td>
                <td class="table-detail">8:00</td>';

        $this->browse(function (Browser $browser) use ($user, $addMonth,$code) {
            $browser->loginAs($user)
                ->visit('/attendance/list')
                ->press('翌月') //翌月ボタンを押下
                ->waitFor('.selected-month')
                ->assertSeeIn('.selected-month', $addMonth->format('Y/m')) //表示年月の確認
                ->assertSourceHas($code); //翌月の情報の表示確認
        });
    }

    public function test_success_transition_attendance_detail(){
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        // $now = Carbon::now();
        $now = Carbon::now()->startOfMonth();

        // $this->browse(function (Browser $browser) use ($user, $now) {
        //     $browser->loginAs($user)
        //         ->visit('/attendance/list')
        //         ->with('.table-row', function (Browser $tableRow) use ($now) {
        //             $tableRow->with('.table-detail', function (Browser $tableDetail) use ($now) {
        //                 $tableDetail->assertSee($now->format('m/d'));
        //             })
        //             ->press('詳細');
        //         })
        //         ->assertTitle('勤怠詳細画面（一般ユーザー）')
        //         ->screenshot('filename')
        //         ->assertSeeIn('.year',$now->format('Y年'))
        //         ->assertSee(($now->format('n月j日'))); // 選択した日付の勤怠詳細画面への遷移確認
        // });
        $targetDate = $now->format('m/d');

        $this->browse(function (Browser $browser) use ($user, $targetDate, $now) {
            $browser->loginAs($user)
                ->visit('/attendance/list')
                ->waitFor('.table-row'); // 念のため待つ

            $rows = $browser->elements('.table-row');
            foreach($rows as $row){
                if(str_contains($row->getText(),$targetDate)){
                    $browser->within($browser->element('.table-detail'), function (Browser $scopeRow) {
                        $scopeRow->press('詳細');
                    });
                    break;
                }
            }
                // ->each(function ($element) use ($browser, $targetDate) {
                //     if (str_contains($element->getText(), $targetDate)) {
                //         $browser->within($element, function (Browser $row) {
                //             $row->press('詳細');
                //         });
                //         return false; // 終了
                //     }
                // })
            $browser->assertTitle('勤怠詳細画面（一般ユーザー）')
                ->screenshot('filename')
                ->assertSeeIn('.year', $now->format('Y年'))
                ->assertSee($now->format('n月j日'));
        });
    }

}
