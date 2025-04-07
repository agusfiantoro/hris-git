<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\TimeAttendance\LeaveSetting\MassLeave\MassLeaveController;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\Employee::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //schedule tiap command diset tiap per 5 menitan, cek di crontab docker, caranya :
        //1. masuk putty, sebagai superadmin, lalu ketikkan comman berikut : docker exec -it php_myborwita bash
        //2. akan masuk container docker, lalu run command : nano /etc/cron.d/crontab
        //3. akan terlihat /5 * * * * /usr/local/bin/php /var/www/hris-om/project/artisan schedule:run
        //yang menandakan bahwa schedule laravel akan dijalankan tiap 5 menitan, tapi eksekusi commandnya tergantung dari command dibawah ini tiap jam berapa

        $schedule->command('transition:cron')->timezone('Asia/Jakarta')->dailyAt('00:05')->runInBackground();
        $schedule->command('employee:massleave')->timezone('Asia/Jakarta')->dailyAt('00:10')->runInBackground();
        $schedule->command('employee:massleaverequest')->timezone('Asia/Jakarta')->dailyAt('00:15')->runInBackground();
        $schedule->command('employee:updateworkdayslocation')->timezone('Asia/Jakarta')->dailyAt('00:20')->runInBackground();
        $schedule->command('employee:contract_reminder')->timezone('Asia/Jakarta')->dailyAt('00:25')->runInBackground();
        $schedule->command('employee:acting_reminder')->timezone('Asia/Jakarta')->dailyAt('00:30')->runInBackground();
        $schedule->command('integration:salescode')->timezone('Asia/Jakarta')->dailyAt('00:45')->runInBackground();
        $schedule->command('employee:generateworkdays')->timezone('Asia/Jakarta')->dailyAt('01:30')->runInBackground();
        // $schedule->command('employee:holiday')->timezone('Asia/Jakarta')->dailyAt('02:30')->runInBackground();
        $schedule->command('integration:oasys')->timezone('Asia/Jakarta')->dailyAt('03:20')->runInBackground();
        $schedule->command('integration:oasys')->timezone('Asia/Jakarta')->dailyAt('12:20')->runInBackground();
        $schedule->command('employee:workdays')->timezone('Asia/Jakarta')->dailyAt('03:30')->runInBackground();
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
