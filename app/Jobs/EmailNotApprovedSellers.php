<?php

namespace App\Jobs;

use Mail;

use App\EmployeeRequest;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotApprovedSellers extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $approvedEmployee;
    private $jg_job;
    private $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jg_job)
    {
        $this->jg_job = $jg_job;
        $this->subject = 'Your application was not approved';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $employeeRequests = EmployeeRequest::where('job_id', $this->jg_job)->where('status', 'pending')->get();
        foreach ($employeeRequests as $employeeRequest){
            $employee = $employeeRequest->employee()->get()->first();
            Mail::send('emails.seller_application_was_not_approved', function($u) use ($employee)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($employee->email);
                $u->subject('Your application was not approved');
            });
        }
    }
}
