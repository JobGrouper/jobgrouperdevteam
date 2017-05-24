<?php

namespace App\Jobs;

use Mail;
use DB;

use App\User;
use App\Interfaces\PaymentServiceInterface;

use App\Jobs\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripePlanActivation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $psi;
    public $seller_account;
    public $plan;
    public $stripe_job;

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
    public function handle(PaymentServiceInterface $psi)
    {

	$testing = false;

	if ($this->plan == 'test') {
	   $testing = true;
	}

	// Making a copy
	$job = $this->stripe_job;
	$plan = $this->plan;
	$employee_account = $this->seller_account;
	
	// Gather everyone
	//$buyers = $this->stripe_job->confirmed_buyers()->get();
	$buyers = $this->stripe_job->confirmed_buyers()->with('early_bird_buyers')->get();

        //
	foreach ($buyers as $buyer) {

		if (!$testing) {

			if ($buyer->early_bird_buyer != NULL 
				&& $buyer->early_bird_buyer->status == 'working') {

				// Update early bird subscription
				$customer = $psi->retrieveCustomerFromUser($buyer, $job, $employee_account->id);
				$subscription = $psi->retrieveSubscription($plan, $customer, $employee_account);
				$psi->changeSubscriptionPlan($subscription, $plan);

				// end early_bird_buyer
				$buyer->early_bird_buyer->status = 'ended';
				$buyer->early_bird_buyer->save();
			}
			else {
				// Create a new subscription
				//
				$customer_record = DB::table('stripe_connected_customers')->where('user_id', '=', $buyer->id)->
					where('managed_account_id', '=', $this->seller_account->id)->first();

				$customer = $psi->retrieveCustomer($customer_record->id, $this->seller_account->id);

				// create subscription
				$response = $psi->createSubscription($this->plan, $customer, $this->seller_account);
			}
		}

		// Send email to user, saying that 
		// the job has begun and their first payment 
		// will be made automatically a day from now
		Mail::send('emails.buyer_job_begun',['job_name'=> $this->stripe_job->title ], function($u) use ($buyer, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Job: ' . $job->title . ' has begun');
		});
	}

	$seller = DB::table('users')->where('id', $this->seller_account->user_id)->first();

	// send email to seller, saying that plan has been completed
	Mail::send('emails.seller_job_begun', ['job_name' => $this->stripe_job->title ], function($u) use ($seller, $job)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to( $seller->email );
 	    $u->subject('Job: ' . $job->title . ' has begun');
	});

	// send email to admin, saying that plan has been completed
	Mail::send('emails.admin_job_begun', ['job_name' => $this->stripe_job->title ], function($u) use ($job)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to('admin@jobgrouper.com');
 	    $u->subject('Job: ' . $job->title . ' has begun');
	});
    }

    public function failed() {
	Log::error("STRIPE PLAN ACTIVATION FAILED: job->" . $this->stripe_job->id);
    }
}
