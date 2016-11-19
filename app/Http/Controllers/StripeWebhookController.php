<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\PaymentServiceInterface;

use App\Http\Requests;

class StripeWebhookController extends Controller
{
    //
	//
	public function createTransfer(PaymentServiceInterface $psi) {

	}

	public function updateFee(PaymentServiceInterface $psi) {

	}

	public function confirmManagedAccount(PaymentServiceInterface $psi) {

	}
}
