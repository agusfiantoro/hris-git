<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Integration\Bgen\BgenController;
use App\Http\Controllers\Integration\Bgen\OasysController;

class OasysIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration:oasys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize OASYS';

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
            \Log::channel('bgen')->info('Start Scheduler: Sync OASYS');
            $bgen = new OasysController();
            // $result = $bgen->syncToBgen(null, true);
            $result = $bgen->syncUpdateToBgen(null);
            if(array_key_exists('errors', $result)) {
                throw new \Exception(json_encode($result['errors']));
            }
            \Log::channel('bgen')->info('Stop Scheduler: Sync OASYS');
            // dd($result);
        } catch(\Exception $e) {
            \Log::channel('bgen')->error('[ERROR] Sync OASYS finished with errors: '.$e->getMessage());
            \Log::channel('bgen')->info('Stop Scheduler: Sync OASYS');
            // dd($e->getMessage());
        }

        
    }
}