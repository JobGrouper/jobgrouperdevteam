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

		/*
		 ! calculate markup
		 create new plan
		 create stripe subscription
		 update other early birds
		 */
		//
		// $job;
		$employee = $job->employee()->first();
		$buyer = $early_bird_buyer->user()->first();

		$current_early_bird_buyers = $job->early_bird_buyers()->where('status', 'working')->get();
	
		// Calculate markup
		//
		// 	// number of early_bird_buyers
		$current_early_bird_count = count($current_early_bird_buyers);

		// 	// min clients count
		$min_clients_count = $job->min_clients_count;
		
		// Add application fee to plan
		$surcharge = $job->salary * .15;

		if ($current_early_bird_count > 0) {
			$xtra_markup = $job->salary * (.15 * ( $current_early_bird_count / $min_clients_count ));
		}
		else {
			$xtra_markup = $job->salary * .15;
		}

		//
		$total_price_will_be = $job->salary + $surcharge + $xtra_markup;

		/////////////
		// Create new plan
		$new_plan = $this->psi->createPlanBare($employee, $job);

		/////////////
		// Create Stripe Subscription
		//
		$employee_account = $this->psi->retrieveAccountFromUser($employee);
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
			$subscription = $this->psi->retrieveSubscription($new_plan, $customer, $employee_account);
			$this->psi->changeSubscriptionPlan($subscription, $new_plan);
		}

		// And that's the end!
	}
}

