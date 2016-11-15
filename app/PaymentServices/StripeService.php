<?php

namespace App\PaymentServices;

use App\Interfaces\PaymentServiceInterface;

use Stripe\Account;
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

	public function createCreditCardToken(array $creditCardData) {
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

	public function createAccount(array $stripeAccountData) {
		$response = Account::create(array(
			"managed" => true,
			"country" => $stripeAccountData['country'],
			"email" => $stripeAccountData['email'],
		));

		return $response;
	}

	public function updateAccount($stripeAccountID, array $stripeAccountData) {
		$account = Account::retrieve($stripeAccountID);

		if($stripeAccountData['legal_entity']['address']){
			$account->legal_entity->address->city = $stripeAccountData['legal_entity']['address']['city'];
			$account->legal_entity->address->line1 = $stripeAccountData['legal_entity']['address']['line1'];
			$account->legal_entity->address->postal_code = $stripeAccountData['legal_entity']['address']['postal_code'];
			$account->legal_entity->address->state = $stripeAccountData['legal_entity']['address']['state'];
		}

		if($stripeAccountData['legal_entity']['dob']){
			$account->legal_entity->dob->day = $stripeAccountData['legal_entity']['dob']['day'];
			$account->legal_entity->dob->month = $stripeAccountData['legal_entity']['dob']['month'];
			$account->legal_entity->dob->year = $stripeAccountData['legal_entity']['dob']['year'];
		}

		if($stripeAccountData['legal_entity']['ssn_last_4']) {
			$account->legal_entity->ssn_last_4 = $stripeAccountData['legal_entity']['ssn_last_4'];
		}

		if($stripeAccountData['legal_entity']['type']) {
			$account->legal_entity->type = $stripeAccountData['legal_entity']['type'];
		}

		if($stripeAccountData['legal_entity']['date']) {
			$account->legal_entity->date = $stripeAccountData['legal_entity']['date'];
		}

		if($stripeAccountData['legal_entity']['ip']) {
			$account->legal_entity->ip = $stripeAccountData['legal_entity']['ip'];
		}

		$response = $account->save();
		return $response;
	}

	public function createExternalAccount(){

	}
	
	public function createPayment() {

	}

	public function createSubscription() {

	}
}

?>
