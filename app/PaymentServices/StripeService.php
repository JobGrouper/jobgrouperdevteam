<?php

namespace App\PaymentServices;

use App\Interfaces\PaymentServiceInterface;

use Stripe\Account;
use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Token;
use \Stripe\Customer;
use \Stripe\Plan;

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

	/*
	 * Creates a Plan associated with a Seller (Managed Account) 
	 * Starts a background process that: 
	 * 	- gathers customers, 
	 * 	- creates customers associated with Managed Account 
	 * 	- Subscribes customers to plan 
	 * 	- sends emails about start of billing to customers and admin
	 *
	 * @params
	 * 	(string) account_id
	 * 	(job) job
	 *	(array) plan_params
	 * @return
	 * 	...
	 * @throws
	 * 	Exceptions for missing parameters
	 */
	public function createPlan($user, $job, $plan_params) {

		if (!is_string($account_id)) {
			throw new \Exception('StripeService::createPlan - Account Id must be a string');
		}

		if (!is_array($plan_params)) {
			throw new \Exception('StripeService::createPlan - Plan parameters must be an array');
		}

		$managed_account = DB::table('stripe_managed_accounts')->where('user_id', $user->id);

		// Create plan
		$plan = Plan::create(array(
		  "amount" => $job->salary,
		  "interval" => "month",
		  "name" => $plan_params["name"],
		  "currency" => $plan_params["currency"],
		  "id" => $plan_params["id"]),
		  array("stripe_account" => $managed_account->id)
		);

		// Add plan to database
		DB::table('stripe_plans')->insert(
			['id' => $plan->id , 'activated' => 1]
		);

		// Queue up subscription job
		$this->dispatch(new StripePlanActivation($this));

		// Email admin that plan is being created
		//
		/*
		Mail::send('emails.plan_activating',['token'=>'asdasdasdasd'],function($u)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to('admin@jobgrouper.com');
		    $u->subject('Job creation started');
		});
		 */

		return 1;
	}

	public function createSubscription() {

	}
}

?>
