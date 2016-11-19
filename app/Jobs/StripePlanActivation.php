<?php

namespace App\Jobs;

use Mail;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripePlanActivation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $psi;
    private $seller_account;

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
	$this->job = $job;
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
	$buyers = $this->job->buyers();

        //
	foreach ($buyers as $buyer) {

		// create customer
		$customer = $this->psi->createCustomer($buyer, $this->seller_account);

		// create subscription
		$this->psi->createSubscription($this->plan, $customer, $this->seller_account);
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
