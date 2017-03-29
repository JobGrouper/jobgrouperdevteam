<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestStripePlanActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stripe_plan_activation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Live test of Stripe Plan Activation';

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
    public function handle(\PaymentServiceInterface $psi)
    {

	/// Create a fake job
	    //
	$job = Job::create([
	    'title' => $request->title,
	    'description' => $request->description,
	    'salary' => $request->salary,
	    'min_clients_count' => $request->min_clients_count,
	    'max_clients_count' => $request->max_clients_count,
	    'category_id' => $request->category_id,
	    'is_dummy' => $request->is_dummy != NULL ? $request->is_dummy : 0
	]);

	// Assign an employee to the job
	$job->employee_id = NULL;
	$job->save();

	// Add a buyer to the job
        $order = $user->orders()->create($input);
	$order->status = 'in_progress';
	$order->card_set = True;
        $order->save();

	// Create a plan
	
	dispatch(new \App\Jobs\StripePlanActivation($psi, $job, NULL, NULL) );
    }
}
