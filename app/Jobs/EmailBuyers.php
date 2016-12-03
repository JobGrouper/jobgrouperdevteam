<?php

namespace App\Jobs;

use Mail;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailBuyers extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $employee;
    private $jg_job;
    private $concern;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($employee, $jg_job, $concern)
    {
	//
	$this->employee = $employee;
	$this->jg_job = $jg_job;
	$this->concern = $concern;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	$subject = NULL;
	$email_file = NULL;
	$parameters = NULL;

	// Gather everyone
	$buyers = $this->jg_job->buyers()->get();
	
	foreach($buyers as $buyer) {

		if ($this->concern == 'employee_approved') {

		    $order = $buyer->orders->where('job_id', $this->jg_job->id)->
				where('status', 'pending')->first();

		    $subject = 'The Job: ' . $this->jg_job->title . " Is Ready for Purchase";
		    $email_file = 'buyer_employee_approved';
		    $parameters = ['employee_name' => $this->employee->full_name,
				'job_name' => $this->jg_job->title, 'order_id' => $order];
		}

		// Send email to user 
		Mail::send('emails.' . $email_file, $parameters, function($u) use ($buyer, $subject)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject($subject);
		});
	}
    }
}
