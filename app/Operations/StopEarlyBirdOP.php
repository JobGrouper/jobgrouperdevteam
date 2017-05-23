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

class StopEarlyBirdOP extends Operation {

	protected $psi;

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	public function go() {

		/*
		 set stripe subscription to inactive (in db)
		 cancel stripe subscription
		 create new plan
		 update other early birds
		 emails
		 */
		$user;
		$employee;
		$job;
		$plan;

		// DEACTIVATE STRIPE SUBSCRIPTION IN DB
		$customer = $this->psi->retrieveCustomer($user);
		$subscription = $this->psi->retrieveSubscription();

		
		DB::table('stripe_subscriptions')->update(['active', 0])->where('plan_id', $plan->id)
			->andWhere('connected_customer_id', $user);

		// End early bird
		$early_bird->status = 'ended';
		$early_bird->save();


		// Cancel Subscription in Stripe
		//
		$this->psi->cancelSubscription($plan, $customer, $employee['get_account_id']);

		if ($there_are_still_early_birds) {

			// calculate markup
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

			////////////
			//
			// Update subscriptions for other early birds
			//
			foreach($early_bird_buyers as $prevvy_buyer) {
				$psi->changeSubscriptionPlan($prevvy_buyer);
			}
		}

	}
}
