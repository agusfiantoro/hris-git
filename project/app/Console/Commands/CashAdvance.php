<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmailController;

class CashAdvance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cashadvance:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settlement Reminder';

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
        try {
            \Log::channel('scheduler')->info('Start Scheduler: Settlement Reminder');
            $email = new EmailController();
            $email->settlementReminder();
        } catch(\Exception $e) {
            \Log::channel('scheduler')->info('Stop Scheduler: Settlement Reminder [ERROR]: '. $e->getMessage());
        }

        
    }
}