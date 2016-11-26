<?php

namespace App\PaymentServices;

use App\Interfaces\PaymentServiceInterface;

use App\Jobs\StripePlanActivation;

use \Carbon\Carbon;
use Mail;
use DB;

use \Stripe\Account;
use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Token;
use \Stripe\Customer;
use \Stripe\Plan;
use \Stripe\Subscription;
use \Stripe\Invoice;
use \Stripe\Transfer;

class StripeService implements PaymentServiceInterface {

	private $stripe_object;

	public function __construct() {
		$this->initialize();
	}

	public function initialize() {
		Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
	}

	public function createCreditCardToken(array $creditCardData, $is_managed=False) {

		$currency = NULL;

		if ($is_managed)
		  $currency = "usd";

		try {
			$creditCardToken = Token::create(
				array(
					"card" => array(
						"number" => $creditCardData['number'],
						"exp_month" => $creditCardData['exp_month'],
						"exp_year" => $creditCardData['exp_year'],
						"cvc" => $creditCardData['cvc'],
						"currency" => $currency
					)
				)
			);
		} catch (Exception $e) {
			dd($e->getMessage());
		}

		return $creditCardToken;
	}

	public function createAccount(array $stripeAccountData, $user_id, $returning=False) {

		$response = Account::create(array(
			"managed" => true,
			"country" => $stripeAccountData['country'],
			"email" => $stripeAccountData['email'],
			"legal_entity" => array(
				"address" => array(
					"city" => $stripeAccountData['legal_entity']['address']['city'],
					"line1" => $stripeAccountData['legal_entity']['address']['line1'],
					"postal_code" => $stripeAccountData['legal_entity']['address']['postal_code'],
					"state" => $stripeAccountData['legal_entity']['address']['state']
				),
				"dob" => array(
					"day" => $stripeAccountData['legal_entity']['dob']['day'],
					"month" => $stripeAccountData['legal_entity']['dob']['month'],
					"year" => $stripeAccountData['legal_entity']['dob']['year']
				),
				"first_name" => $stripeAccountData['legal_entity']['first_name'],
				"last_name" => $stripeAccountData['legal_entity']['last_name'],
				"ssn_last_4" => $stripeAccountData['legal_entity']['ssn_last_4'],
				"type" => $stripeAccountData['legal_entity']['type']),
			"tos_acceptance" => array(
				"date" => $stripeAccountData['tos_acceptance']['date'],
				"ip" => $stripeAccountData['tos_acceptance']['ip'])
			)
		);

		$this->insertAccountIntoDB($response['id'], $user_id);

		if ($returning) {
		  return $response;
		}
		else
		  return 1;
	}

	public function insertAccountIntoDB($account_id, $user_id) {

		// store account in database
		DB::table('stripe_managed_accounts')->insert(array(
			'id' => $account_id,
			'user_id' => $user_id)
		);
	}

	public function updateAccount($stripeAccountID, array $stripeAccountData) {
		$account = Account::retrieve($stripeAccountID);

		$keys = array_keys($stripeAccountData);

		foreach($keys as $key) {

			// LEGAL ENTITY
			if ($key == 'legal_entity') {

				$legal_entity_keys = array_keys($stripeAccountData[$key]);

				foreach ($legal_entity_keys as $le_key) {

					// DATE OF BIRTH
					if ($le_key == 'dob') {
						
						$dob_keys = array_keys($stripeAccountData[$key][$le_key]);

						foreach ($dob_keys as $dob_key) {
							$account->$key->$le_key->$dob_key = $stripeAccountData[$key][$le_key][$dob_key];
						}
					}
					// ADDRESS
					else if ($le_key == 'address') {

						$address_keys = array_keys($stripeAccountData[$key][$le_key]);

						foreach ($address_keys as $address_key) {
							$account->$key->$le_key->$address_key = $stripeAccountData[$key][$le_key][$address_key];
						}
					}
					else {
						// otherwise
						$account->$key->$le_key = $stripeAccountData[$key][$le_key];
					}
				}
			}
			// TERMS OF ACCEPTANCE
			else if ($key == 'tos_acceptance') {
				$tos_keys = array_keys($stripeAccountData[$key]);

				foreach ($tos_keys as $tos_key) {
					$account->$key->$tos_key = $stripeAccountData[$key][$tos_key];
				}
			}
			else {
				$account->$key = $stripeAccountData[$key];
			}
		}

		/*
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

		if($stripeAccountData['tos_acceptance']['date']) {
			$account->tos_acceptance->date = $stripeAccountData['tos_acceptance']['date'];
		}

		if($stripeAccountData['tos_acceptance']['ip']) {
			$account->tos_acceptance->ip = $stripeAccountData['tos_acceptance']['ip'];
		}
		 */ 

		$response = $account->save();
		return $response;
	}

	/*
	 * A delete managed account function (for testing purposes, mainly)
	 *
	 */
	public function deleteAccount($account_id) {

		$account = Account::retrieve($account_id);
		$response = $account->delete();

		return $response['deleted'];
	}

	/*
	 * A function deleting the account from the database (for testability)
	 */
	public function deleteAccountFromDB($account_id) {

		// delete account in database
		DB::table('stripe_managed_accounts')->where('id', '=', $account_id)->delete();
	}

	/*
	 * Creates a Customer object; used on registration and on new subscriptions
	 * We'll know if it's being used for subscriptions if account id is passed along with it
	 *
	 */
	public function createCustomer($user, array $customerData, $account_id=NULL) {

		$customer_data = array();
		$data_keys = array_keys($customerData);

		foreach($data_keys as $key) {
			$customer_data[$key] = $customerData[$key];
		}

		// Make api call
		$response = NULL;

		if (!$account_id) {
			$response = Customer::create($customer_data);
		}
		else {
			$response = Customer::create($customer_data, array('stripe_account' => $account_id));
		}

		$this->createCustomerInDB($user, $response, $account_id);

		return $response;

	}

	public function createCustomerInDB($user, $customer, $account_id=NULL) {

		if ($account_id) {

			$root = DB::table('stripe_root_customers')->where('user_id', '=', $user->id)->first();

			DB::table('stripe_connected_customers')->insert([
				'id' => $customer['id'],
				'user_id' => $user->id,
				'root_customer_id' => $root->id,
				'managed_account_id' => $account_id
				]);
		}
		else {

			DB::table('stripe_root_customers')->insert([
				'id' => $customer['id'],
				'user_id' => $user->id,
				]);
		}
	}

	/*
	 * Deletes created customer
	 */
	public function deleteCustomer($user, $account_id=NULL) {

		// 1) Retrieve from db
		// 2) Retrieve from stripe
		// 3) Delete
		//

		if ($account_id) {

			$customer_record = DB::table('stripe_connected_customers')->where('user_id', '=', $user->id)->
				where('managed_account_id', '=', $account_id)->first();

			$customer = Customer::retrieve(array('id' => $customer_record->id),
				array('stripe_account' => $account_id));
			$response = $customer->delete();

			$this->deleteCustomerFromDB($user);
		}
		else {
			$customer_record = DB::table('stripe_root_customers')->where('user_id', '=', $user->id)->first();
			$customer = Customer::retrieve($customer_record->id);
			$response = $customer->delete();

			$this->deleteCustomerFromDB($user, $account_id);
		}

		return $response;
	}

	/*
	 * Deletes created customer from database
	 */
	public function deleteCustomerFromDB($user, $account_id=NULL) {

		if ($account_id) {
			DB::table('stripe_connected_customers')->where('user_id', '=', $user->id)->
				where('managed_account_id', '=', $account_id)->delete();
		}
		else {
			DB::table('stripe_connected_customers')->where('user_id', '=', $user->id)->delete();
		}
	}

	/*
	 * Creates external account associated with a managed account

	 * @params
	 * 	account_id, string account_token
	 * @returns
	 * 	response
	 * @throws
	 * 	...
	 *
	 */
	public function createExternalAccount($user, $token){

		$account_record = DB::table('stripe_managed_accounts')->where('user_id', '=', $user->id)->first();
		$account = Account::retrieve($account_record->id);

		$response = $account->external_accounts->create(array("external_account" => $token));

		$this->createExternalAccountInDB($account['id'], $response['id'], $response['last4']);

		return $response;
	}

	public function createExternalAccountInDB($account_id, $card_id, $card_last4) {

		DB::table('stripe_external_accounts')->insert([
			'id' => $card_id,
			'managed_account_id' => $account_id,
			'last_four' => $card_last4
			]);
	}

	public function deleteExternalAccount($user_id, $card_id) {

		$account_record = DB::table('stripe_managed_accounts')->where('user_id', '=', $user_id)->first();
		$account = Account::retrieve($account_record->id);

		// delete account
		$response = $account->external_accounts->retrieve($card_id)->delete();
		
		$this->psi->deleteExternalAccountInDB($card_id);

		return $response;
	}

	public function deleteExternalAccountInDB($card_id) {
		DB::table('stripe_external_accounts')->where(
			'id', '=', $card_id)->delete();
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
	public function createPlan($user, $job, $testing=False) {

		/*
		if (!is_string($account_id)) {
			throw new \Exception('StripeService::createPlan - Account Id must be a string');
		}

		if (!is_array($plan_params)) {
			throw new \Exception('StripeService::createPlan - Plan parameters must be an array');
		}
		 */

		// Retrieve account
		$managed_account = DB::table('stripe_managed_accounts')->where('user_id', '=', $user->id)->first();

		$plan_id = $this->generatePlanId($job->title);

		// Create plan
		$plan = Plan::create(array(
		  "amount" => $job->salary * 100, // value must be in cents for Stripe
		  "interval" => "month",
		  "name" => $job->title,
		  "currency" => 'USD',
		  "id" => $plan_id),
		  array("stripe_account" => $managed_account->id)
		);

		// Add plan to database
		DB::table('stripe_plans')->insert(
			['id' => $plan->id , 'managed_account_id' => $managed_account->id,
			'job_id' => $job->id, 'activated' => 1]
		);

		// Queue up subscription job
		dispatch(new StripePlanActivation($this, $job, $plan, $managed_account));

		if (!$testing) {

			// Queue up subscription job
			dispatch(new StripePlanActivation($this, $job, $plan, $managed_account));

			// Email admin that plan is being created
			//
			Mail::send('emails.plan_activating',['token'=>'asdasdasdasd'],function($u)
			{
			    $u->from('admin@jobgrouper.com');
			    $u->to('admin@jobgrouper.com');
			    $u->subject('Job creation started');
			});
		}

		return $plan;
	}

	/*
	 * Generates a unique id for a plan
	 */
	public function generatePlanId($plan_name) {

		return  'plan_' . random_int(10000000, 99999999) . substr( hash('md5', $plan_name), 20);
	}

	public function deletePlan($user, $job, $account_id) {

		$plan_record = DB::table('stripe_plans')->where('job_id', '=', $job->id)->first();

		$plan = Plan::retrieve(array('id' => $plan_record->id),
			array('stripe_account' => $account_id));

		$response = $plan->delete();

		$this->deletePlanFromDB($plan_record);

		return $response;
	}

	public function deletePlanFromDB($plan) {
		DB::table('stripe_plans')->where('id', '=', $plan->id)->delete();
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

		if (is_object($plan)) {
		  $plan_id = $plan->id;
		}

		if (is_array($customer)) {
		  $customer_id = $customer['id'];
		}

		if (is_object($customer)) {
		  $customer_id = $customer->id;
		}

		if (is_array($seller_account)) {
		  $account_id = $seller_account['id'];
		}

		if (is_object($seller_account)) {
		  $account_id = $seller_account->id;
		}

		$response = Subscription::create(
			array('customer' => $customer_id,
			'plan' => $plan_id,
			'application_fee_percent' => 15,
			'trial_end' => Carbon::tomorrow()->timestamp // starting a day after so we can modify invoice
		),
			array('stripe_account' => $account_id)
		);

		// some kind of error mechanism in case this fails
		if (isset($response['error'])) {

		}

		$this->createSubscriptionInDB($response['id'], $plan_id, $customer_id);

		return $response;
	}

	public function createSubscriptionInDB($id, $plan_id, $customer_id) {

		// Add subscription to database
		DB::table('stripe_subscriptions')->insert(
			['id' => $id , 'plan_id' => $plan_id,
			'connected_customer_id' => $customer_id, 'activated' => 1]
		);
	}

	public function cancelSubscription($plan, $customer, $account_id) {

		$subscription_record = DB::table('stripe_subscriptions')->where('plan_id', '=', $plan['id'])->
			where('connected_customer_id', '=', $customer['id'])->first();

		$subscription = Subscription::retrieve(array('id' => $subscription_record->id),
			array('stripe_account' => $account_id));

		$response = $subscription->cancel();

		$this->deleteSubscriptionInDB($subscription_record);

		return $response;
	}

	public function deleteSubscriptionInDB($subscription) {
		DB::table('stripe_subscriptions')->where('id', '=', $subscription->id)->delete();
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
	public function updateCustomerSource($user, $token, $account_id=NULL) {
		
		if ($account_id) {

			$customer_record = DB::table('stripe_connected_customers')->
						where('user_id', '=', $user->id)->
						where('managed_account_id', '=', $account_id)->first();
			
			$root_record = DB::table('stripe_root_customers')->
						where('user_id', '=', $user->id)->first();
		}
		else {
			$customer_record = DB::table('stripe_root_customers')->
						where('user_id', '=', $user->id)->first();
		}

		if ($account_id) {

			// retrieve customer
			// 	- get id
			// 	- get object
			//
			$customer = Customer::retrieve(array('id' => $customer_record->id), 
						array('stripe_account' => $account_id));

			$root = Customer::retrieve($root_record->id);

			$customer->source = $token;
			$response = $customer->save();

			// Generate a new token (because we can't reuse them)
			$root_response = $root->sources->create(array('source' => $response['sources']['data'][0]['id']));
			//$root_response = $root->save();

			$this->updateCustomerSourceInDB($response['sources']['data'][0]['id'], $customer_record->id, 
				$response['sources']['data'][0]['last4'], $root_record->id );
		}
		else {
			$customer = Customer::retrieve($customer_record->id);
			$customer->source = $token;
			$response = $customer->save();

			$this->updateCustomerSourceInDB($response['sources']['data'][0]['id'], $customer_record->id, 
				$response['sources']['data'][0]['last4'] );
		}

		return $response;
	}

	public function updateCustomerSourceInDB($source_id, $customer_id, $last_four, $root_id=NULL) {

		if ($root_id) {
			// insert into db
			DB::table('stripe_customer_sources')->insert( 
				['id' => $source_id, 'root_customer_id' => $root_id,
				'connected_customer_id' => $customer_id,
				'last_four' => $last_four] );
		}
		else {
			// insert into db
			DB::table('stripe_customer_sources')->insert( 
				['id' => $source_id, 'root_customer_id' => $customer_id,
				'last_four' => $last_four] );
		}
	}

	public function deleteCustomerSource() {

		
	}

	public function deleteCustomerSourceInDB() {

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

	/*
	 * Creates a transfer between a buyer's managed account 
	 *   and it's external account(bank account/debit card).
	 */
	public function createTransfer($user, $job) {

		// get the plan
		$account_record = DB::table('stripe_managed_accounts')->
			where('user_id', '=', $user->id)->first();

		$account = Account::retrieve($account_record->id);

		// api call
		$response = Transfer::create(array(
		  "amount" => $job->salary * 100,
		  "currency" => "usd",
		  "destination" => "default_for_currency",
		  ),
  		array('stripe_account' => $account['id'])
		);

		// store in database
		$this->createTransferInDB($response['id'], $account['id']);

		return $response;
	}

	public function createTransferInDB($transfer_id, $account_id) {

		DB::table('stripe_transfers')->insert(
			['id' => $transfer_id, 'managed_account_id' => $account_id]
		);
	}
}

?>
