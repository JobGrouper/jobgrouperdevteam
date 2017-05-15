<?php

namespace App\Operations;

use App\Skeleton\Operation;

use App\Interfaces\PaymentServiceInterface as PaymentServiceInterface;
use App\Jobs\Job;
use App\StripeManagedAccount;

use App\User;

class CreateRefundOP extends Operation {

	public function __construct(PaymentServiceInterface $psi) {
		$this->psi = $psi;
	}

	// weakness: what if there are no invoices, no charges?
	public function go(User $employee = NULL, User $buyer = NULL) {

		$refund = $this->psi->createRefund($employee->managed_account_id, $buyer->id);
		return $refund;
	}
}
