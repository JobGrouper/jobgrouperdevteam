<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripePlanActivation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $psi;

    /**
     * Create a new job instance.
     *
     * @return void
     * @params PaymentServiceInterface psi
     */
    public function __construct($psi)
    {
        //
	$this->psi = $psi;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
