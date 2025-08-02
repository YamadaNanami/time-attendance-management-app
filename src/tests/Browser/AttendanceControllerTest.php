<?php

namespace Tests\Browser;

use App\Models\Timecard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AttendanceControllerTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * ID:4
     */
    public function test_see_current_datetime(){
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        // 現在日時の取得
        $japaneseWeekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekdayNumber = Carbon::now()->format('w');
        $japaneseWeekday = $japaneseWeekdays[$weekdayNumber];

        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('n');
        $day = Carbon::now()->format('j');

        $date = $year.'年'.$month.'月'.$day.'日('.$japaneseWeekday.')';
        $time = Carbon::now()->format('H:i');

        $this->browse(function (Browser $browser) use ($user,$date, $time) {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.date')
                ->assertSeeIn('.date',$date) //現在日付の確認
                ->waitFor('.time')
                ->assertSeeIn('.time',$time); //現在時刻の確認
        });
    }

    /**
     * ID:5
     */
    public function test_see_current_status_off(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.status')
                ->assertSeeIn('.status','勤務外'); //ステータスの確認
        });
    }

    public function test_see_current_status_at_work(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.status')
                ->assertSeeIn('.status','出勤中'); //ステータスの確認
        });
    }

    public function test_see_current_status_taking_a_break(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '8:00:00',
                'type' => 1
            ],[
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '12:00:00',
                'type' => 2
            ]
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.status')
                ->assertSeeIn('.status','休憩中'); //ステータスの確認
        });
    }

    public function test_see_current_status_finished_work(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '8:00:00',
                'type' => 1
            ],[
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '18:00:00',
                'type' => 4
            ]
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.status')
                ->assertSeeIn('.status','退勤済'); //ステータスの確認
        });
    }

    /**
     * ID:6
     */
    public function test_success_at_work(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn')
                ->assertSeeIn('.form-btn','出勤') //ボタンの表示確認
                ->press('出勤')
                ->waitFor('.status')
                ->assertSeeIn('.status','出勤中'); //ステータスの確認
        });
    }

    public function test_not_see_at_work_btn(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            [
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '8:00:00',
                'type' => 1
            ],
            [
                'user_id' => $user->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'time' => '18:00:00',
                'type' => 4
            ]
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.text')
                ->assertNotPresent('.form-btn')
                ->assertSee('出勤'); //出勤ボタンが表示されないかの確認
        });
    }

    public function test_confirm_clock_in_time_admin(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $adminUser = User::factory()->create([
            'name' => 'test admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn')
                ->press('出勤')
                ->press('ログアウト');
        });

        $clockIn = substr(
            Timecard::where('user_id', $user->id)
                ->where('date', Carbon::now()->format('Y-m-d'))
                ->value('time')
            ,0,5
        );

        $code = '<td class="table-detail">'.$user->name.'</td>
                    <td class="table-detail">'.$clockIn.'</td>';

        $this->browse(function (Browser $browser) use($adminUser,$code) {
            $browser->loginAs($adminUser)
                ->visit('/admin/attendance/list')
                ->assertSeeIn('.selected-date', Carbon::now()->format('Y/m/d')) //日付の表示確認
                ->assertSourceHas($code); //対象ユーザーの出勤時間の確認
        });
    }

    /**
     * ID:7
     */
    public function test_success_break_in(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn.bg-white')
                ->assertSeeIn('.form-btn.bg-white','休憩入') //ボタンの表示確認
                ->press('休憩入') //休憩の処理
                ->waitFor('.status')
                ->assertSeeIn('.status','休憩中'); //ステータスの確認
        });
    }

    public function test_can_break_in_many_times(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩入')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩戻')
                ->waitFor('.form-btn.bg-white')
                ->assertSeeIn('.form-btn.bg-white','休憩入'); //ボタンの表示確認
        });
    }

    public function test_success_break_out(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩入')
                ->waitFor('.form-btn.bg-white')
                ->assertSeeIn('.form-btn.bg-white','休憩戻') //ボタンの表示確認
                ->press('休憩戻')
                ->waitFor('.status')
                ->assertSeeIn('.status','出勤中'); //ステータスの確認
        });
    }

    public function test_can_break_out_many_times(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::insert([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩入')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩戻')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩入')
                ->assertSeeIn('.form-btn.bg-white','休憩戻'); //ボタンの表示確認
        });
    }

    public function test_confirm_total_break_time_admin(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $adminUser = User::factory()->create([
            'name' => 'test admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        Timecard::insert([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn.bg-white')
                ->press('休憩入')
                ->waitFor('.form-btn.bg-white')
                ->pause(120000) //2分間待機
                ->press('休憩戻')
                ->press('ログアウト');
        });

        $date = Carbon::now()->format('Y-m-d');

        $breakIn = Carbon::createFromFormat('Y-m-d H:i:s',$date.' '.Timecard::where('user_id', $user->id)
            ->where('date', $date)
            ->where('type', 2)
            ->value('time'));

        $breakOut = Carbon::createFromFormat('Y-m-d H:i:s',$date.' '.Timecard::where('user_id', $user->id)
            ->where('date', $date)
            ->where('type', 3)
            ->value('time'));

        $diffMinutes = $breakIn->diffInMinutes($breakOut);

        //時:分 に変換
        $hours = floor($diffMinutes / 60);
        $minutes = str_pad($diffMinutes % 60, 2, '0', STR_PAD_LEFT);

        $totalBreakTime = $hours . ':' . $minutes;

        $code = 'td class="table-detail">'.$user->name.'</td>
                    <td class="table-detail">08:00</td>
                    <td class="table-detail"></td>
                    <td class="table-detail">'.$totalBreakTime.'</td>';

        $this->browse(function (Browser $browser) use($adminUser,$code) {
            $browser->loginAs($adminUser)
                ->visit('/admin/attendance/list')
                ->assertSeeIn('.selected-date', Carbon::now()->format('Y/m/d')) //日付の表示確認
                ->assertSourceHas($code); // 対象ユーザーの休憩時間の確認
        });
    }

    /**
     * ID:8
     */
    public function test_success_leaving_work(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        Timecard::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => '8:00:00',
            'type' => 1
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn')
                ->assertSeeIn('.form-btn','退勤') //ボタンの表示確認
                ->press('退勤')
                ->waitFor('.status')
                ->assertSeeIn('.status','退勤済'); //ステータスの確認
        });
    }

    public function test_confirm_clock_out_time_admin(){
        $user = User::factory()->create([
            'name' => 'テスト 花子',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 1
        ]);

        $adminUser = User::factory()->create([
            'name' => 'test admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 2
        ]);

        $this->browse(function (Browser $browser) use($user)  {
            $browser->loginAs($user)
                ->visit('/attendance')
                ->waitFor('.form-btn')
                ->press('出勤')
                ->waitFor('.form-btn')
                ->pause(120000) //2分間待機
                ->press('退勤')
                ->press('ログアウト');
        });

        $date = Carbon::now()->format('Y-m-d');

        $clockIn = Timecard::where('user_id',$user->id)
            ->where('date',$date)
            ->where('type',config('constants.TIME_TYPE.CLOCK_IN'))
            ->value('time');
        $clockOut = Timecard::where('user_id',$user->id)
            ->where('date',$date)
            ->where('type',config('constants.TIME_TYPE.CLOCK_OUT'))
            ->value('time');

        $code = 'td class="table-detail">'.$user->name.'</td>
                    <td class="table-detail">'.substr($clockIn,0,5).'</td>
                    <td class="table-detail">'.substr($clockOut,0,5).'</td>';

        $this->browse(function (Browser $browser) use($adminUser,$code) {
            $browser->loginAs($adminUser)
                ->visit('/admin/attendance/list')
                ->assertSeeIn('.selected-date', Carbon::now()->format('Y/m/d')) //日付の表示確認
                ->assertSourceHas($code); // 対象ユーザーの退勤時間の確認
        });
    }
}
