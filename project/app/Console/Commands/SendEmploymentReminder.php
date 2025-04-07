<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\EmailController;
use App\Models\Curl;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendEmploymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:contract_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Employee Contract Reminder';

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
        \Log::channel('scheduler')->info('Start Schedule : Employee End Reminder');
        $emailController = new EmailController(); 
        \Log::channel('scheduler')->info($emailController->end_employee());
        \Log::channel('scheduler')->info('Stop Schedule : Employee End Reminder');
        \Log::channel('scheduler')->info('Start Scheduler: Inactive Employee by Expired Date');
        $expiredContract = DB::table('hr_employee as he')
            ->join('master_general_data as mgd', 'he.id_employment_status', 'mgd.id_general_data')
            ->join('master_users as mu', 'he.id_user', 'mu.id_user')
            ->select('mu.*', 'he.nik_employee')
            ->where('he.status', 'A')
            ->whereRaw('he.expired_date::date < now()::date')
            ->where('mgd.code', 'Contract')
            ->get();
        $apiIsActiveUser = env('APP_ENV') == 'production' ?
                         Curl::findApi('myborwita_user_update_is_active_production')
                         : Curl::findApi('myborwita_user_update_is_active_staging');
        foreach($expiredContract as $expired) {
            $user = MasterUser::find($expired->id_user);
            $user->status = 'I';
            $user->updated_by = 1;
            if($user->save()) {
                $msg = 'Inactive Expired User: '.$expired->nik_employee.' ';
                $dataApiIsActiveUser = [
                    'nik'=> $expired->nik_employee, 
                    'is_active'=> false,
                ];
                $callApiIsActiveUser = Http::withBasicAuth(@$apiIsActiveUser->user, @$apiIsActiveUser->password)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->put(@$apiIsActiveUser->url, $dataApiIsActiveUser);
                if($callApiIsActiveUser->successful()) {
                    $msg .= '- API Inactive Success';
                } else {
                    $msg .= '- API Inactive Success';
                }
                \Log::channel('scheduler')->info($msg);
            }
        }
        \Log::channel('scheduler')->info('Stop Scheduler: Inactive Employee by Expired Date');
    }
}
