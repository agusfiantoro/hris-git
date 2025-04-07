<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController;

class Workdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:workdays';

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
        // // \Log::channel('scheduler')->info('Start Schedule : Patch Workdays');
        // // $workdays2->patchWorkdays(); //Menambal Workdays yang masih belum tergenerate
        // // \Log::channel('scheduler')->info('Stop Schedule : Patch Workdays');
        // $workdays2->generateWorkdaysBulky();
        
        return 0;
    }
}
