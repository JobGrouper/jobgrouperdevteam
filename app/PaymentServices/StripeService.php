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

	public  function createCreditCardToken(array $creditCardData) {
		try {
			$creditCardToken = Token::create(
				array(
					"card" => array(
						"number" => $creditCardData['number'],
						"exp_month" => $creditCardData['exp_month'],
						"exp_year" => $creditCardData['exp_year'],
						"cvc" => $creditCardData['cvc']
					)
				)
			);
		} catch (Exception $e) {
			dd($e->getMessage());
		}

		return $creditCardToken;
	}

	public function createPayment() {

	}

	public function createSubscription() {

	}
}

?>
