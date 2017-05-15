<?php

namespace App\Operations;

use App\Skeleton\Operation;

use App\Operations\CreateRefundOP;

use App\EmployeeExitRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Jobs\Job;
use App\StripeManagedAccount;

class EmployeeExitOP extends Operation {

	public function __construct() {

	}

	public function go(EmployeeExitRequest $er = NULL) {

	    //Approving EmployeeExitRequest
	    $employeeExitRequest->status = 'approved';
	    $employeeExitRequest->save();

	    //Getting employee`s job, and buyers
	    $job = $employeeExitRequest->job()->first();
	    $buyers = $job->buyers()->get();
	    $previousEmployee = $job->employee()->first();

	    //Getting employee`s managed account
	    $managedAccount = StripeManagedAccount::where('user_id', $previousEmployee->id)->first();
	    $previousEmployee->managed_account_id = $managedAccount->id;

	    //Make potential employee as main employee (if potential employee exists)
	    $job->employee_id = null;
	    if($job->potential_employee_id){
		$job->employee_id = $job->potential_employee_id;
		$job->potential_employee_id = null;
	    }
	    $job->save();

	    foreach ($buyers as $buyer){

		// create refund
		$op = new CreateRefundOP();
		//$psi->createRefund($previousEmployee->id, $buyer->id);
		$op->go($previousEmployee, $buyer);

		// cancel subscription
		$plan = Plan::where('job_id', $job->id)->where('managed_account_id', $managedAccount->id);
		$customer = $psi->retrieveCustomerFromUser($buyer, $job, $managedAccount->id);
		$psi->cancelSubscription($plan, $customer, $managedAccount->id);

		// Replace employer on job
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

	    // TODO
	    // // modify db to save employee exit requests, make 'processed' state or something
	    $job->employee_requests()->where('employee_id', $employeeExitRequest->employee_id)->delete();
	}

}
