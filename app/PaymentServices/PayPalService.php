<?php

namespace App\PaymentServices;

use App\Interfaces\PaymentServiceInterface;

use PayPal\Api\Amount;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;

class PayPalService implements PaymentServiceInterface {

	private $apiContext;

	public function __construct() {
		$this->initialize();
	}

	public function initialize() {

		//Get payment for first month
		$this->apiContext = new \PayPal\Rest\ApiContext(
		    new \PayPal\Auth\OAuthTokenCredential(
			env('PAYPAL_CLIENT_ID'),     // ClientID
			env('PAYPAL_CLIENT_SECRET')      // ClientSecret
		    )
		);
	}

	public function createCreditCardToken() {

		// Stub
	}

	public function createPayment() {

		// Stub
	}

	public function createSubscription() {
		
		// Stub
	}
}

?>
