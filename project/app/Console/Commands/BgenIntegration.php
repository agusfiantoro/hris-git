<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Integration\Bgen\BgenController;
use App\Models\Integration\Bgen\Bgen;

class BgenIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration:salescode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Sales Code with ERP';

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
            \Log::channel('bgen')->info('Start Scheduler: Sync to ERP');
            $inactives = DB::select("SELECT he.nik_employee, mgd.description AS reason, bisc.* FROM hr_career_transaction hct 
                        JOIN master_general_data mgd ON hct.id_terminate_reason = mgd.id_general_data
                        JOIN integration.bgen_integration_sales_code bisc ON hct.id_employee = bisc.id_employee 
                        JOIN hr_employee he ON bisc.id_employee = he.id_employee
                        WHERE mgd.description NOT IN('Contract - Proses Rehire PKWT', 'Permanent - Proses Rehire PKWTT')
                        AND mgd.code = 'Terminate'
                        AND hct.creation_date >= now() - INTERVAL '2 days'");
            foreach($inactives as $inactive) {
                $data = Bgen::findOrFail($inactive->id_integration_sales_code);
                $data->status = 'I';
                $data->updated_by = 1;
                $data->save();
                $bgen = new BgenController();
                $result = $bgen->syncToBgen($inactive->nik_employee, true);
            }
            
            // if(array_key_exists('errors', $result)) {
            //     throw new \Exception(json_encode($result['errors']));
            // }
            \Log::channel('bgen')->info('Stop Scheduler: Sync to ERP');
        } catch(\Exception $e) {
            \Log::channel('bgen')->error('[ERROR] Sync to ERP finished with errors: '.$e->getMessage());
            \Log::channel('bgen')->info('Stop Scheduler: Sync to ERP');
        }

        
    }
}