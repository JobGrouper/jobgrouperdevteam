<?php

namespace App\Jobs;

use Mail;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripeTransferUpdated extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, StripeTransferEvent;

    protected $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
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
	$data['previous_attributes'] = $this->event['data']['previous_attributes'];

	// send mail
	Mail::send('emails.seller_transfer_updated', ['data' => $data], function($u) use ($employee)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($employee->email);
	    $u->subject('Transfer Updated');
	});
    }
}
