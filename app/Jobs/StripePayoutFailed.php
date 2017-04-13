<?php

namespace App\Jobs;

use Mail;
use Log;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Interfaces\PaymentServiceInterface;

use App\Traits\StripePayoutEvent;

class StripePayoutFailed extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, StripePayoutEvent;

    protected $event;

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
    public function handle(PaymentServiceInterface $psi)
    {
        // data
	$data = $this->getEventVariables($this->event);
	$data['failure_code'] = $this->event['data']['object']['failure_code'];
	$data['failure_message'] = $this->event['data']['object']['failure_message'];

	$employee = NULL;

	// If we're testing, provide fake employee data
	if ($data['account_id'] == "acct_00000000000000") {

		$employee = new \StdClass();
		$employee->email = 'admin@jobgrouper.com';
		Log::info('StripeTransferFailed: test sent');
	}
	else {
		// get employee
		$employee = $psi->retrieveUserFromAccount( $data['account_id'] );
	}

	Mail::send('emails.seller_payout_failed', ['data' => $data], function($u) use ($employee)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($employee->email);
	    $u->subject('Payout Failed. Please re-enter bank details');
	});
    }

    public function failed() {
	Log::error("StripePayoutFailed Job Failed.");
	Log::error(print_r($this->event, true));
    }
}
