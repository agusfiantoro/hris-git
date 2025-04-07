<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncUpdateBizApproval implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nik;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $nikEmployee)
    {
        $this->nik = $nikEmployee;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sync = new \App\Http\Controllers\Integration\Bgen\OasysController();
        $result = $sync->syncUpdateToBgen($this->nik, false, false);
        Log::channel('mobile')->info("Biz Approval Firebase update [$this->nik]: ".json_encode($result));
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->nik;
    }
}
