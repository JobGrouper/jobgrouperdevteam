<?php

namespace App\PaymentServices;

use App\Interfaces\PaymentServiceInterface;

use \Carbon\Carbon;

use \Stripe\Account;
use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Token;
use \Stripe\Customer;
use \Stripe\Plan;
use \Stripe\Subscription;
use \Stripe\Invoice;

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
		$this->dispatch(new StripePlanActivation($this, $job, $plan, $managed_account));

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

	/*
	 * Creates a Subscription associated with a Plan; 
	 *   should only be called inside createPlan function
	 *
	 * @params
	 * 	plan
	 * 	customer
	 * 	seller_account
	 * @throws
	 * @returns
	 * 	...
	 *
	 */
	public function createSubscription($plan, $customer, $seller_account) {

		$plan_id = NULL;
		$customer_id = NULL;
		$account_id = NULL;
		
		if (is_array($plan)) {
		  $plan_id = $plan['id'];
		}

		if (is_array($customer)) {
		  $customer_id = $customer['id'];
		}

		if (is_array($seller_account)) {
		  $account_id = $seller_account['id'];
		}

		$response = Subscription::create(
			array('customer' => $customer_id,
			'plan' => $plan_id,
			'application_fee_percent' => 15,
			'trial_end' => Carbon::tomorrow() // starting a day after so we can modify invoice
		),
			array('stripe_account' => $account_id)
		);

		// some kind of error mechanism in case this fails
		if (isset($response['error'])) {

		}

		return 1;
	}

	/*
	 * Adds a source (credit/debit card) to an existing Customer object
	 * If an original id is included, add source to original Customer object
	 *
	 * @params
	 * 	source - token provided by Stripe.JS
	 * 	customer_id - id of Stripe Customer
	 * 		+ may refer to root customer or connected customer depending on
	 * 		+ presence of original id
	 * 	original_id - id of original Stripe Customer (nullable)
	 * @throws
	 * @returns
	 * 	...
	 */
	public function updateCustomerSource($source, $customer_id, $original_id=NULL) {

		$customer_data = array(
			'token' => $source,
			'root_customer_id' => NULL,
			'connected_customer_id' => NULL
		);

		if (isset($original_id)) {
			$customer_data['root_customer_id'] = $original_id;
			$customer_data['connected_customer_id'] = $customer_id;
		}
		else {
			$customer_data['root_customer_id'] = $customer_id;
		}

		// retrieve customer
		// 	- get id
		// 	- get object
		//
		$customer = Customer::retrieve($customer_id);

		$response = $customer->sources->create(array("source" => $source));

		if ($original != NULL) {
			$root_customer = Customer::retrieve($original);
			$response = $customer->sources->create(array("source" => $source));
		}

		// insert into db
		DB::table('stripe_customer_sources')->insert( $customer_data );

		return 1;
	}

	/*
	 * Modifies a customer's upcoming invoice:
	 * 	- to include our application fee
	 * 	- to prorate the amount due
	 *
	 * 	Can happen at subscription creation or during webhook event (on invoice created)
	 *
	 * 	If customer_id is set, it means this is happening during subscription creation, 
	 * 	and the upcoming invoice must be retrieved first
	 *
	 * @params
	 * 	params
	 * 	customer_id
	 * @throws
	 * @returns
	 * 	...
	 *
	 */
	public function modifyInvoice($params, $input_object) {

		// Get upcoming invoice

		$invoice = NULL;

		// If it's a string, assume its a customer id
		if (is_string($input_object)) {
		  $invoice = Invoice::upcoming(array("customer" => $customer_id));
		}

		// If it's an array, assume it's an invoice object
		if (is_array($input_object)) {
			$invoice = $input_object;
		}

		// Set values for invoice
		foreach(array_keys($params) as $key) {
			$invoice->$key = $params[$key];
		}

		// Save invoice
		$invoice->save();

		return 1;
	}
}

?>
