<?php

namespace App\Operations;

use App\Skeleton\Operation;

use App\Operations\CreateRefundOP;

use App\EmployeeExitRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Job;
use App\EarlyBirdBuyer;
use App\StripeManagedAccount;

use \Stripe\Plan;

use DB;
use Mail;

class StartNewEarlyBirdOP extends Operation {

	protected $psi;

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	public function go(Job $job = NULL, EarlyBirdBuyer $early_bird_buyer = NULL) {

		//
		$employee = $job->employee()->first();
		$buyer = $early_bird_buyer->user()->first();
		$current_early_bird_buyers = $job->early_bird_buyers()->where('status', 'working')->get();
	
		/////////////
		// Create new plan
		$new_plan = $this->psi->createPlanBare($employee, $job, array(
			'amount' => $job->early_bird_markup * 100));

		/////////////
		// Create Stripe Subscription
		//
		$employee_account = $this->psi->retrieveAccountFromUser($employee);
		$old_plan = $this->psi->retrievePlan($job, $employee_account->id);
		$customer = $this->psi->retrieveCustomerFromUser($buyer, $job, $employee_account->id);
		$new_subscription = $this->psi->createSubscription($new_plan, $customer, $employee_account);

		// Set buyer to working
		$early_bird_buyer->status = 'working';
		$early_bird_buyer->save();

		////////////
		//
		// Update subscriptions for other early birds
		//
		foreach($current_early_bird_buyers as $prevvy_buyer) {
			//TEST:: $subscription = $this->psi->retrieveSubscription($new_plan, $customer, $employee_account);
			$customer = $this->psi->retrieveCustomerFromUser($prevvy_buyer, $job, $employee_account->id);
			$subscription = $this->psi->retrieveSubscription($old_plan, $customer, $employee_account);
			$this->psi->changeSubscriptionPlan($subscription, $new_plan);
		}

		// And that's the end!
	}
}

