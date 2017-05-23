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

class EndAllEarlyBirdsOP extends Operation {

	protected $psi;

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	public function go(Job $job = NULL, $options=NULL) {

		/*
		 * 
		 */
		$job->endAllEarlyBirds();

		/*
		 * Maybe deactivate all Stripe subscriptions
		 * -	- if employee is leaving
		 * Update all Stripe subscriptions
		 * - 	- if job is starting
		 */

		// And that's the end!
	}
}

