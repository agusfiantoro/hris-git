<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EmailController;

class SendActingReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:acting_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Employee Acting Reminder';

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
        $emailController = new EmailController(); 
        \Log::channel('scheduler')->info('Start Schedule : Acting End Reminder');
        \Log::channel('scheduler')->info($emailController->end_employee_acting());
        \Log::channel('scheduler')->info('Stop Schedule : Acting End Reminder');

        \Log::channel('scheduler')->info('Start Schedule : Probation End Reminder');
        \Log::channel('scheduler')->info($emailController->end_employee_acting("Probation"));
        \Log::channel('scheduler')->info('Stop Schedule : Probation End Reminder');
    }
}
