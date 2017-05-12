<?php

namespace App\Jobs;

use App\EmployeeExitRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Jobs\Job;
use App\StripeManagedAccount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;
use Stripe\Plan;

class CreateRefund extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PaymentServiceInterface $psi)
    {
        //Getting employee`s exit requests that are older than 2 weeks
        $employeeExitRequests = EmployeeExitRequest::where('status', 'pending')->where('created_at','<',date('Y-m-d H:i:s', strtotime('-2 weeks')))->get();
        foreach ($employeeExitRequests as $employeeExitRequest){

            //Approving EmployeeExitRequest
            $employeeExitRequest->status = 'approved';
            $employeeExitRequest->save();

            //Getting employee`s job, and buyers
            $job = $employeeExitRequest->job()->first();
            $buyers = $job->buyers()->get();
            $previousEmployee = $job->employee()->first();

            //Getting employee`s managed account
            $managedAccount = StripeManagedAccount::where('user_id', $previousEmployee->id)->first();


            //Make potential employee as main employee (if potential employee exists)
            $job->employee_id = null;
            if($job->potential_employee_id){
                $job->employee_id = $job->potential_employee_id;
                $job->potential_employee_id = null;
                $job->work_start();
            }
            $job->save();

            foreach ($buyers as $buyer){
                $psi->createRefund($previousEmployee->id, $buyer->id);
                $plan = Plan::where('job_id', $job->id)->where('managed_account_id', $managedAccount->id);
                $customer = $psi->retrieveCustomerFromUser($buyer, $job, $managedAccount->id);
                $psi->cancelSubscription($plan, $customer, $managedAccount->id);

                //If there is new employer
                if($job->employee_id){
                    $account = $psi->retrieveAccount($managedAccount->id);
                    $psi->createCustomer($buyer, $job, ['email' => $buyer->email], $account);
                }
                
                if($buyers->count() <= 10){
                    //todo send email to employee about refund
                }
            }

            if($buyers->count() > 10){
                //todo send email to employee about all refunds
            }

            $job->employee_requests()->where('employee_id', $employeeExitRequest->employee_id)->delete();

            $job->work_stop();
        }
    }

    public function failed() {
        Log::error("CreateRefund Job Failed.");
    }
}
