<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController;

class Employee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transition:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transition Employee By Cron';

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
        $career_schedule = new CareerTransitionController(); 
        $startTime = now();
        \Log::channel('scheduler')->info('Start Scheduler: Employee Transition');
        \Log::channel('scheduler')->info($career_schedule->transition());
        $elapsedTime = gmdate('H:i:s', now()->diffInSeconds($startTime));
        \Log::channel('scheduler')->info('Stop Schedule : Employee Transition. Execution duration: '.$elapsedTime);
    }
}
