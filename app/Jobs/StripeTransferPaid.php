<?php

namespace App\Jobs;

use Mail;
use Log;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Traits\StripeTransferEvent;

class StripeTransferPaid extends Job implements ShouldQueue
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
        //
	$data = $this->getEventVariables($this->event);

	$employee = NULL;

	// If we're testing, provide fake employee data
	if ($data['account_id'] == "acct_00000000000000") {

		$employee = new \StdClass();
		$employee->email = 'admin@jobgrouper.com';
	}
	else {
		// get employee
		$employee = $psi->retrieveUserFromAccount( $data['account_id'] );
	}

	// send mail
	Mail::send('emails.seller_transfer_paid', ['data' => $data], function($u) use ($employee)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($employee->email);
	    $u->subject('Transfer Complete!');
	});
    }

    public function failed() {
	Log::error("StripeTransferPaid Job Failed.");
	Log::error(print_r($this->event, true));
    }
}
