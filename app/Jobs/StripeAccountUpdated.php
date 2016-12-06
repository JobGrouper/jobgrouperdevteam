<?php

namespace App\Jobs;

use Mail;
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
    public function handle()
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

		Mail::send('emails.seller_fully_verified', [], function($u) use ($employee)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('You\'re fully verified on JobGrouper!');
		});
	}
	else {

		Mail::send('emails.seller_need_additional_verification', ['id' => $employee->id], function($u) use ($employee)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('You\'re fully verified on JobGrouper!');
		});
	}

    }
}