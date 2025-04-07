<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController;
use Illuminate\Support\Facades\DB;

class GenerateWorkdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:generateworkdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Employee Workdays';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $workdays2 = new WorkDaysController(); 

        \Log::channel('scheduler')->info('Start Schedule : Generate Workdays');
        $workdays = new AttendanceController(); 
        // $workdays->generateWorkdays(); //Create Workdays untuk seluruh employee aktif
        $workdays->generateWorkdaysByDate();
        \Log::channel('scheduler')->info('Start Schedule: Lock GPS Location');
        $currentDate = now()->format('Y-m-d');
        DB::select("select * from generate_lock_gps_location(?, ?, ?, ?, ?)", [
            null, null, $currentDate, $currentDate, 1
        ]);
        \Log::channel('scheduler')->info('Stop Schedule: Lock GPS Location');
        $workdays2 = new WorkDaysController();

        $workdays2->updateWorkdaysByHoliday(); //Mengecek dan mengupdate day_type workdays karyawan yg belum tersetting holiday setelah admin melakukan setting jauh2 hari, rentang pengecekan holiday di bulan sekarang dan bulan depan

        $workdays2->checkDayTypeByIdShift(); //Mengecek dan memperbaiki day_type yg salah berdasar id_shift di work_days

        $workdays2->generateWorkdaysBulky(); // generate workdays bulky
        return 0;
    }
}