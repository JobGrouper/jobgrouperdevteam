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
use Mail;

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

	    $total_refund = 0;

	    foreach ($buyers as $buyer){

		// create refund
		//$psi->createRefund($previousEmployee->id, $buyer->id);
		$op = \App::make('App\Operations\CreateRefundOP');
		$refund = $op->go($previousEmployee, $buyer);

		// increment
		$refund_amount = $this->refundToDollars( $refund->amount );
		$total_refund += $refund_amount;

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

		Mail::send('emails.buyer_refund_employee_exit', ['data' => 
				['job' => $job,
				'employee' => $previousEmployee,
				'refund_amount' => $refund_amount ]], 
		function($u) use ($buyer) {
			$u->from('admin@jobgrouper.com');
			$u->to($buyer->email);
			$u->subject('A refund is on it\'s way');
		});
		
		if($buyers->count() <= 10){
		    //todo send email to employee about refund
			Mail::send('emails.seller_refund_employee_exit', [
					'data' => [
						'job' => $job,
						'buyer' => $buyer,
						'refund_amount' => $refund_amount]], 
			function($u) use ($previousEmployee) {
				$u->from('admin@jobgrouper.com');
				$u->to($previousEmployee->email);
				$u->subject('A refund for one of your buyers has been created');
			});
		}
	    }

	    if($buyers->count() > 10){
		//todo send email to employee about all refunds
		    Mail::send('emails.seller_refund_employee_exit_total', [
		    			'data' => [
						'total_refund' => $total_refund]], 
			function($u) use ($previousEmployee) {
				$u->from('admin@jobgrouper.com');
				$u->to($previousEmployee->email);
				$u->subject('Your buyers are being refunded.');
			});
	    }

	    // TODO
	    // // modify db to save employee exit requests, make 'processed' state or something
	    $job->employee_requests()->where('employee_id', $er->employee_id)->delete();
	}

	/* 
	 * Refunds are given in cents (integer), convert to dollars (float)
	 */
	private function refundToDollars($refund_amount) {
		return $refund_amount / 100;
	}
}
