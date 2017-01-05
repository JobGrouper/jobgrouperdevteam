<?php

namespace App\Jobs;

use App\Interfaces\PaymentServiceInterface;
use App\StripeVerificationRequest;
use Mail;
use Log;
use DB;

use App\User;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripeAccountUpdated extends Job implements ShouldQueue
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
    public function handle(PaymentServiceInterface $psi)
    {
        //
	$account_id = $this->event['data']['object']['id'];
	$verification_status = $this->event['data']['object']['legal_entity']['verification']['status'];
	$verification = $this->event['data']['object']['verification']['disabled_reason'];
	$fields_needed = $this->event['data']['object']['verification']['fields_needed'];

	// Get employee
	$employee_record = DB::table('stripe_managed_accounts')->
		where('id', $account_id)->first();

	$employee = User::find($employee_record->user_id);

	if ($verification_status == 'verified') {

		// set verified status to TRUE
		$employee->verified = true;
		$employee->save();

		Mail::send('emails.seller_fully_verified', [], function($u) use ($employee)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('You are now fully verified!');
		});
	}
	else {

		$stripeVerificationRequest = NULL;

		if(count($fields_needed) > 0) {
		    $stripeVerificationRequest = StripeVerificationRequest::create([
			'managed_account_id' => $account_id,
			'fields_needed' => json_encode($fields_needed),
		    ]);
		}

		// making sure svrequest is present
		if ($stripeVerificationRequest) {

			Mail::send('emails.seller_need_additional_verification', ['request_id' => $stripeVerificationRequest->id], function($u) use ($employee)
			{
			    $u->from('admin@jobgrouper.com');
			    $u->to($employee->email);
			    $u->subject('You\'re not fully verified on JobGrouper!');
			});
		}
		else {
			Log::error("Stripe Verification Request was not created for user: " . $employee->id . "; email: " . $employee->email);
		}
	}

    }
}
