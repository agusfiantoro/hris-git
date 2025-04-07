<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TimeAttendance\LeaveSetting\MassLeave\MassLeaveController;

class Massleave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:massleave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Employee Mass Leave';

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
        \Log::channel('scheduler')->info('Start Schedule : Mass leave');
        $mass_leave = new MassLeaveController(); 
        $mass_leave->generateMassLeaveScheduler();
        \Log::channel('scheduler')->info('Stop Schedule : Mass leave');
    }
}
