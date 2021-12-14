<?php

namespace App\Console;

use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\Events\DeviceEvent;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call(function () {
            $devices = DB::select("select * from devices WHERE (TIMESTAMPDIFF(SECOND, `lastUpdate`, NOW()))>((`update`/1000)*3) AND `status`='on'");
            DB::update("UPDATE `devices` SET `status`='off' WHERE (TIMESTAMPDIFF(SECOND, `lastUpdate`, NOW()))>((`update`/1000)*3)");
            foreach ($devices as $device) {
                event(new DeviceEvent($device->id));
                $users = DB::select("SELECT `id_user` FROM `notifications_devices` WHERE `value` = 1 and `id_device` = :id", ['id' => $device->id]);
                foreach ($users as $userid){
                    $user = DB::select("SELECT * FROM `users` WHERE `id` = :id ", ['id' => $userid->id_user]);
                    Mail::raw('Статус устройства '.$device->name.' off', function ($message) use ($user) {
                        $message->from('ran@impuls-perm.ru', 'meteo');
                        $message->to($user[0]->email)->subject('Статус устройства изменился');
                    });
                }

            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
