<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController;

class Holiday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:holiday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Employee Holiday and Check Day Type';

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
        $workdays2 = new WorkDaysController();

        $workdays2->updateWorkdaysByHoliday(); //Mengecek dan mengupdate day_type workdays karyawan yg belum tersetting holiday setelah admin melakukan setting jauh2 hari, rentang pengecekan holiday di bulan sekarang dan bulan depan

        $workdays2->checkDayTypeByIdShift(); //Mengecek dan memperbaiki day_type yg salah berdasar id_shift di work_days

        return 0;
    }
}