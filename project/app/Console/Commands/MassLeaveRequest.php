<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TimeAttendance\Leaves\MassLeaveRequestController;
use Illuminate\Http\Request;

class MassLeaveRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:massleaverequest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Employee Mass Leave Request';

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
        $request = new Request();
        $massLeaveController = new MassLeaveRequestController(); 
        $massLeaveController->executeMassLeaveRequest($request);
    }
}
