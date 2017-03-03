<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Job;
use App\Interfaces\PaymentServiceInterface;

class StopJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:stop {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stops a job and deletes the plan associated with it';

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
     * @return mixed
     */
    public function handle(PaymentServiceInterface $psi)
    {
        // get job
        $job = Job::find( $this->argument('id') );
	$employee = $job->employee()->first();
	$stripe_account = $psi->retrieveAccountFromUser($employee);

	// set status to 'waiting'
	$job->status = 'waiting';
	$job->save();

	$psi->deletePlan(NULL, $job, $stripe_account['id']); // todo remove user from delete plan

	return true;
    }
}
