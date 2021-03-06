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

class AdjustAllEarlyBirdsOP extends Operation {

	protected $psi;

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	public function go(Job $job = NULL) {

		/*
		 *
		 */
		$employee = $job->employee()->first();

		$current_early_bird_buyers = $job->early_bird_buyers()->with('user')->where('status', 'working')->get();

		$employee_account = $this->psi->retrieveAccountFromUser($employee);
		$old_plan = $this->psi->retrievePlan($job, $employee_account->id);

		if (count($current_early_bird_buyers) > 0) {

			/////////////
			// Create new plan
			$new_plan = $this->psi->createPlanBare($employee, $job, array(
				'amount' => $job->early_bird_markup * 100));

			////////////
			//
			// Update subscriptions for other early birds
			//
			foreach($current_early_bird_buyers as $prevvy_buyer) {
				$customer = $this->psi->retrieveCustomerFromUser($prevvy_buyer->user, $job, $employee_account->id);
				$subscription = $this->psi->retrieveSubscription($old_plan, $customer, $employee_account);
				$this->psi->changeSubscriptionPlan($subscription, $new_plan);

				Mail::queue('emails.early_bird_buyers_rate_changed_to_buyer', ['data' => 
					['job' => $job]], function($u) use ($prevvy_buyer)
				{
					$u->from('admin@jobgrouper.com');
					$u->to($prevvy_buyer->user->email);
					$u->subject('Early Bird Rate Change');
				});
			}

			Mail::queue('emails.early_bird_buyers_rate_changed_to_employee', ['data' => 
				['job' => $job]], function($u) use ($employee)
			{
				$u->from('admin@jobgrouper.com');
				$u->to($employee->email);
				$u->subject('Early Bird Rate Change');
			});

			///////////////
			// Deactivate old plan
			//
			$this->psi->deactivatePlan( $old_plan );
		}

	}
}

