<?php

namespace App\Jobs;

use Mail;
use Log;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Traits\StripeTransferEvent;

class StripeTransferFailed extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, StripeTransferEvent;

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
    public function handle()
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

	Mail::send('emails.seller_transfer_failed', ['data' => $data], function($u) use ($employee)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($employee->email);
	    $u->subject('Transfer Failed. Please re-enter bank details');
	});
    }

    public function failed() {
	Log::error("StripeTransferFailed Job Failed.");
	Log::error(print_r($this->event, true));
    }
}
