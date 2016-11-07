<?php

namespace App\PaymentServices;

use App\Interfaces\PaymentServiceInterface;

use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Token;
use \Stripe\Customer;

class StripeService implements PaymentServiceInterface {

	private $stripe_object;

	public function __construct() {
		$this->initialize();
	}

	public function initialize() {

		Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
	}

	public function createCreditCardToken() {

	}

	public function createPayment() {

	}

	public function createSubscription() {

	}
}

?>
