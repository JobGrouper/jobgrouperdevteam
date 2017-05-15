<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Interfaces\PaymentServiceInterface;

use App\Job;
use App\User;

use Queue;

class TestStripePlanActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stripe_plan_activation {user_id} {employee_id}';

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
    public function handle(PaymentServiceInterface $psi)
    {

	/// Create a fake job
	    //
	$job = Job::create([
	    'title' => 'A Test Job (already deleted)',
	    'description' => 'But not for looooooong...',
	    'salary' => 15,
	    'min_clients_count' => 15,
	    'max_clients_count' => 2,
	    'category_id' => 1,
	    'is_dummy' => 1
	]);

	$buyer = User::findOrFail($this->argument('user_id'));
	$employee = new \StdClass();
	$employee->id = $this->argument('employee_id');
	$employee->user_id = $this->argument('employee_id');

	// Assign an employee to the job
	$job->employee_id = $employee->id;
	$job->save();

	// Add a buyer to the job
	$order = $buyer->orders()->create([
		'job_id' => $job->id,
		'buyer_id' => $buyer->id,
		'status' => 'in_progress',
		'card_set' => 1
		]);

	// Create a plan
	dispatch( new \App\Jobs\StripePlanActivation($psi, $job, 'test', $employee) );
	Queue::push(function() use ($order, $job) {
		$order->delete();
		$job->delete();
	});
    }
}
