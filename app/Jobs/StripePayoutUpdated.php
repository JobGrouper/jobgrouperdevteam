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

class StripePayoutUpdated extends Job implements ShouldQueue
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
        //
	$data = $this->getEventVariables($this->event);
	$data['previous_attributes'] = $this->event['data']['previous_attributes'];

	$employee = NULL;

	// count the number of attributes that were modified
	$data['modified_count'] = count(array_keys($data['previous_attributes']));
	$inline_count = 0;

	$data['modified'] = array(
		'arrival_date' => false,
		'amount' => false,
		'other' => false
	);

	if (isset( $data['previous_attributes']['arrival_date'] )) {
		$data['modified']['arrival_date'] = true;
		$inline_count++;
	}

	if (isset( $data['previous_attributes']['amount'] )) {
		$data['modified']['amount'] = true;
		$inline_count++;
	}

	// If the first two have been found, then an attribute
	//  has changed that we don't provide a message for
	//
	if ($inline_count < $data['modified_count']) {
		$data['modified']['other'] = true;
	}

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
	Mail::send('emails.seller_payout_updated', ['data' => $data], function($u) use ($employee)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($employee->email);
	    $u->subject('Payout Updated');
	});
    }

    public function failed() {
	Log::error("StripePayoutUpdated Job Failed.");
	Log::error(print_r($this->event, true));
    }
}
