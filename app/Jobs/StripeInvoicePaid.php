<?php

namespace App\Jobs;

use Mail;
use DB;

use App\Jobs\Job;

use App\User;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripeInvoicePaid extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        //
	$this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

	$account_id = $this->event['user_id'];
	$customer_id = $this->event['data']['object']['customer'];
	$plan_id = $this->event['data']['object']['lines']['data'][0]['plan']['id'];

	// Check for test parameter
	if ($account_id == "acct_00000000000000") {

		if ($verification_status == 'verified') {
			Mail::send('emails.seller_payment_successful', [], function($u)
			{
			    $u->from('admin@jobgrouper.com');
			    $u->to('test@test.com');
			    $u->subject('TEST INVOICE PAID RECEIVED');
			});
		}

	}
	else {

		// GET BUYER AND SELLER
		//
		// -- make this into a join later
		//
		$buyer_record = DB::table('stripe_connected_customers')->
			where('id', $customer_id)->
			where('managed_account_id', $account_id)->first();

		$buyer = User::find($buyer_record->user_id);

		// Get employee
		$employee_record = DB::table('stripe_managed_accounts')->
			where('id', $account_id)->first();

		$employee = User::find($employee_record->user_id);

		// Get plan
		$plan_record = DB::table('stripe_plans')->
			where('id', $plan_id)->first();

		$job = \App\Job::find($plan_record->job_id);

		Mail::send('emails.buyer_payment_successful', ['employee' => $employee->full_name, 'job' => $job], function($u) use ($buyer, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Your payment for '. $job->title . ' has gone through!');
		});
		
		Mail::send('emails.seller_payment_successful', [], function($u) use ($employee, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('A payment for '. $job->title . ' has gone through!');
		});
	}
    }

    public function failed() {
	$account_id = $this->event['user_id'];
	Log::error("STRIPE INVOICE PAID FAILED: account->" . $account_id);
    }
}
