<?php

namespace App\Operations;

use App\Skeleton\Operation;

use App\Operations\CreateRefundOP;

use App\EmployeeExitRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Jobs\Job;
use App\StripeManagedAccount;

use \Stripe\Plan;

use DB;

class EmployeeExitOP extends Operation {

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	public function go(EmployeeExitRequest $er = NULL) {

	    //Approving EmployeeExitRequest
	    $er->status = 'approved';
	    $er->save();

	    //Getting employee`s job, and buyers
	    $job = $er->job()->first();
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
		//$psi->createRefund($previousEmployee->id, $buyer->id);
		$op = \App::make('App\Operations\CreateRefundOP');
		$op->go($previousEmployee, $buyer);

		// cancel subscription
		$plan_record = DB::table('stripe_plans')->
			where('job_id', $job->id)->
			where('managed_account_id', $managedAccount->id)->first();

		$plan = Plan::retrieve(
			array('id' => $plan_record->id),
			array('stripe_account' => $managedAccount->id)
		);

		$customer = $this->psi->retrieveCustomerFromUser($buyer, $job, $managedAccount->id);
		$this->psi->cancelSubscription($plan, $customer, $managedAccount->id);

		// Replace employer on job
		if($job->employee_id){
		    $account = $this->psi->retrieveAccount($managedAccount->id);
		    $this->psi->createCustomer($buyer, $job, ['email' => $buyer->email], $account);
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
	    $job->employee_requests()->where('employee_id', $er->employee_id)->delete();
	}

}
