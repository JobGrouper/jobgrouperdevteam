<?php

namespace App\Jobs;

use Mail;
use DB;

use App\Jobs\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripePlanActivation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $psi;
    protected $seller_account;
    protected $plan;
    protected $stripe_job;

    /**
     * Create a new job instance.
     *
     * @return void
     * @params PaymentServiceInterface psi
     */
    public function __construct($psi, $job, $plan, $seller_account)
    {
        //
	$this->psi = $psi;
	$this->stripe_job = $job;
	$this->plan = $plan;
	$this->seller_account = $seller_account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	
	// Gather everyone
	$buyers = $this->stripe_job->buyers()->get();

        //
	foreach ($buyers as $buyer) {

		$customer_record = DB::table('stripe_connected_customers')->where('user_id', '=', $buyer->id)->
			where('managed_account_id', '=', $this->seller_account->id)->first();

		$customer = $this->psi->retrieveCustomer($customer_record->id, $this->seller_account->id);

		// create subscription
		$response = $this->psi->createSubscription($this->plan, $customer, $this->seller_account);

		// Send email to user, saying that 
		// the job has begun and their first payment 
		// will be made automatically a day from now
		/*
		Mail::send('emails.plan_activated',['token'=>'asdasdasdasd'],function($u)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to('admin@jobgrouper.com');
		    $u->subject('Job has begun');
		});
		 */
	}

	// send email to admin, saying that plan has been completed
	/*
	Mail::send('emails.plan_activated',['token'=>'asdasdasdasd'],function($u)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to('admin@jobgrouper.com');
	    $u->subject('Job has begun');
	});
	 */
    }
}
