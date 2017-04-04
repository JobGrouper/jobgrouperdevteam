<?php

namespace App\Jobs;

use Mail;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Traits\StripeTransferEvent;

class StripeTransferCreated extends Job implements ShouldQueue
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

	// additional data

	// send mail
	Mail::send('emails.seller_transfer_created', ['data' => $data], function($u) use ($employee)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($employee->email);
	    $u->subject('Transfer Process Initiated');
	});
    }
}
