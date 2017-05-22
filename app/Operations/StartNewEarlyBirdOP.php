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

class StartNewEarlyBirdOP extends Operation {

	protected $psi;

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	public function go() {

		/*
		 ! calculate markup
		 create new plan
		 create stripe subscription
		 update other early birds
		 */
		//
		$employee;
		$job;
	
		// Calculate markup
		
		// 	// number of early_bird_buyers
		$current_early_bird_count = count($early_bird_buyers->where('status', 'working'));

		// 	// min clients count
		$min_clients_count = $job->min_clients_count;
		
		// Add application fee to plan
		$surcharge = $job->salary * .15;
		$amount = ($job->salary + $surcharge) * 100; // value must be in cents for Stripe

		$xtra_markup = (.15 * ( $current_early_bird_count / $min_clients_count ));

		//
		$total_price_will_be = $job->salary + $surcharge + $xtra_markup;

		/////////////
		// Create new plan
		$new_plan = $this->psi->createPlanBare($employee, $job);

		/////////////
		// Create Stripe Subscription
		//
		$customer = $this->psi->retrieveCustomer($employee, $buyer);
		$new_subscription = $this->psi->createSubscription($new_plan, $customer, $employee);

		////////////
		//
		// Update subscriptions for other early birds
		//
		foreach($early_bird_buyers as $prevvy_buyer) {
			$psi->changeSubscriptionPlan($prevvy_buyer);
		}

		// And that's the end!
	}
}

