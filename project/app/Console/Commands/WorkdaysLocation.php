<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Employee\EmployeeSetting\WorkDaysController;

class WorkdaysLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:updateworkdayslocation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Workdays Location by Position';

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
        $workdaysController = new WorkDaysController(); 
        $workdaysController->updateWorkdaysLocation();
    }
}
