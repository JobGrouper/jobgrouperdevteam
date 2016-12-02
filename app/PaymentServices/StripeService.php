<?php

namespace App\PaymentServices;

use Illuminate\Support\Facades\Log;
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

	private $response;

	/*

	STRIPE TRY CATCH TEMPLATE

	try {

	  // Use Stripe's library to make requests...

	} catch(\Stripe\Error\Card $e) {

	  // Since it's a decline, \Stripe\Error\Card will be caught
	  $body = $e->getJsonBody();
	  $err  = $body['error'];

	  print('Status is:' . $e->getHttpStatus() . "\n");
	  print('Type is:' . $err['type'] . "\n");
	  print('Code is:' . $err['code'] . "\n");
	  // param is '' in this case
	  print('Param is:' . $err['param'] . "\n");
	  print('Message is:' . $err['message'] . "\n");

	} catch (\Stripe\Error\RateLimit $e) {

	  // Too many requests made to the API too quickly

	} catch (\Stripe\Error\InvalidRequest $e) {

	  // Invalid parameters were supplied to Stripe's API

	} catch (\Stripe\Error\Authentication $e) {

	  // Authentication with Stripe's API failed
	  // (maybe you changed API keys recently)

	} catch (\Stripe\Error\ApiConnection $e) {

	  // Network communication with Stripe failed

	} catch (\Stripe\Error\Base $e) {

	  // Display a very generic error to the user, and maybe send
	  // yourself an email

	} catch (Exception $e) {

	  // Something else happened, completely unrelated to Stripe

	}
	*/

	public function __construct() {
		$this->initialize();

		$this->response = array(
			'status' => NULL,
			'response_object' => NULL,
			'reject_message' => NULL,
			'reject_message_user' => NULL
		);
	}

	public function initialize() {
		Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
	}

	private function constructErrorResponse($response) {

		// Since it's a decline, \Stripe\Error\Card will be caught
		$body = $response->getJsonBody();
		$error  = $body['error'];

		$formatted_response = array(
			'http' => $response->getHttpStatus(),
			'type' => $error['type'],
			'param' => $error['param'],
			'message' => $error['message'],
			'user' => NULL
		);

		return $formatted_response;
	}

	public function retrieveAccount($account_id) {

		$response = NULL;
		$error_response = NULL;

		try {
			$response = Account::retrieve($account_id);

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		return $response;
	}

	public function retrieveCustomer($customer_id, $account_id=NULL) {

		$response = NULL;
		$error_response = NULL;

		try {
			if ($account_id) {
			  $response = Customer::retrieve(array('id' => $customer_id),
					array('stripe_account' => $account_id));
			}
			else {
			  $response = Customer::retrieve($customer_id);
			}

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		if ($error_response !== NULL)
			return $error_response;
		else
			return $response;
	}

	public function createCreditCardToken(array $creditCardData, $type, $is_managed=False) {

		$response = NULL;
		$error_response = NULL;

		$currency = NULL;

		if ($is_managed)
		  $currency = "usd";

		try {

			if ($type == 'card') {
				$response = Token::create(
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
			}
			else if ($type == 'bank_account') {
				$response = Token::create(
					array(
						"bank_account" => array(
							"country" => "US",
							"currency" => $currency,
							"account_holder_name" => $creditCardData['account_holder_name'],
							"account_holder_type" => "individual",
							"routing_number" => $creditCardData['routing_number'],
							"account_number" => $creditCardData['account_number']
						)
					)
				);

			}

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		if ($error_response) 
			return $error_response;
		else
			return $response;
	}

	public function createAccount(array $stripeAccountData, $user_id, $returning=False) {

		$response = NULL;
		$error_response = NULL;

		try {
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

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}


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
		$response = NULL;
		$error_response = NULL;

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

		try {
			$response = $account->save();

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}
			
		return $response;
	}

	/*
	 * A delete managed account function (for testing purposes, mainly)
	 *
	 */
	public function deleteAccount($account_id) {

		$response = NULL;
		$error_response = NULL;

		$account = Account::retrieve($account_id);
		try {
			$response = $account->delete();
		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

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
	 * Creates a Customer object; used on job subscription / new subscriptions
	 */
	public function createCustomer($user, array $customerData, $account_id=NULL) {

		$response = NULL;
		$error_response = NULL;

		$customer_data = array();
		$data_keys = array_keys($customerData);

		foreach($data_keys as $key) {
			$customer_data[$key] = $customerData[$key];
		}

		// Make api call
		$response = NULL;
		$error_response = NULL;

		try {
			if (!$account_id) {
				$response = Customer::create($customer_data);
			}
			else {
				$response = Customer::create($customer_data, array('stripe_account' => $account_id));
			}
		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		$this->createCustomerInDB($user, $response, $account_id);

		return $response;

	}

	public function createCustomerInDB($user, $customer, $account_id=NULL) {

		if ($account_id) {

			// $root = DB::table('stripe_root_customers')->where('user_id', '=', $user->id)->first();

			DB::table('stripe_connected_customers')->insert([
				'id' => $customer['id'],
				'user_id' => $user->id,
				//'root_customer_id' => $root->id,
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

		$response = NULL;
		$error_response = NULL;

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
		
		$response = NULL;
		$error_response = NULL;

		if ($account_id) {

			$customer_record = DB::table('stripe_connected_customers')->
						where('user_id', '=', $user->id)->
						where('managed_account_id', '=', $account_id)->first();

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

			$customer->source = $token;

			try {
				$response = $customer->save();

			} catch(\Stripe\Error\Card $e) {
				$error_response = $this->constructErrorResponse($e);
				
				// user error
				$error_response['user'] = true;

			} catch (\Stripe\Error\RateLimit $e) {
			  // Too many requests made to the API too quickly
				$error_response = $this->constructErrorResponse($e);

			} catch (\Stripe\Error\InvalidRequest $e) {
			  // Invalid parameters were supplied to Stripe's API
				$error_response = $this->constructErrorResponse($e);

				// user error (possibly)
				$error_response['user'] = true;

			} catch (\Stripe\Error\Authentication $e) {
			  // Authentication with Stripe's API failed
				$error_response = $this->constructErrorResponse($e);

			} catch (\Stripe\Error\ApiConnection $e) {
			  // Network communication with Stripe failed
				$error_response = $this->constructErrorResponse($e);

			} catch (\Stripe\Error\Base $e) {
			   // Generic Stripe error
				$error_response = $this->constructErrorResponse($e);

			} catch (Exception $e) {
			  // Something else happened, completely unrelated to Stripe
				$error_response = $this->constructErrorResponse($e);

			}

			$this->updateCustomerSourceInDB($response['sources']['data'][0]['id'], $customer_record->id, 
				$response['sources']['data'][0]['last4']);
		}
		else {
			$customer = Customer::retrieve($customer_record->id);
			$customer->source = $token;

			try {
				$response = $customer->save();

			} catch(\Stripe\Error\Card $e) {
				$error_response = $this->constructErrorResponse($e);
				
				// user error
				$error_response['user'] = true;

			} catch (\Stripe\Error\RateLimit $e) {
			  // Too many requests made to the API too quickly
				$error_response = $this->constructErrorResponse($e);

			} catch (\Stripe\Error\InvalidRequest $e) {
			  // Invalid parameters were supplied to Stripe's API
				$error_response = $this->constructErrorResponse($e);

				// user error (possibly)
				$error_response['user'] = true;

			} catch (\Stripe\Error\Authentication $e) {
			  // Authentication with Stripe's API failed
				$error_response = $this->constructErrorResponse($e);

			} catch (\Stripe\Error\ApiConnection $e) {
			  // Network communication with Stripe failed
				$error_response = $this->constructErrorResponse($e);

			} catch (\Stripe\Error\Base $e) {
			   // Generic Stripe error
				$error_response = $this->constructErrorResponse($e);

			} catch (Exception $e) {
			  // Something else happened, completely unrelated to Stripe
				$error_response = $this->constructErrorResponse($e);

			}

			$this->updateCustomerSourceInDB($response['sources']['data'][0]['id'], $customer_record->id, 
				$response['sources']['data'][0]['last4'] );
		}

		return $response;
	}

	public function updateCustomerSourceInDB($source_id, $customer_id, $last_four) {

		// insert into db
		DB::table('stripe_customer_sources')->insert( 
			['id' => $source_id,
			'connected_customer_id' => $customer_id,
			'last_four' => $last_four] );
	}

	public function deleteCustomerSource() {

		
	}

	public function deleteCustomerSourceInDB() {

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

		$response = NULL;
		$error_response = NULL;

		$account_record = DB::table('stripe_managed_accounts')->where('user_id', '=', $user->id)->first();
		$account = Account::retrieve($account_record->id);

		try {
			$response = $account->external_accounts->create(array("external_account" => $token));

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

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

		$response = NULL;
		$error_response = NULL;

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

		try {

			// Add application fee to plan
			$surcharge = $job->salary * .15;
			$amount = ($job->salary + $surcharge) * 100; // value must be in cents for Stripe

			// Create plan
			$plan = Plan::create(array(
			  "amount" => $amount, 
			  "interval" => "month",
			  "name" => $job->title,
			  "currency" => 'USD',
			  "id" => $plan_id),
			  array("stripe_account" => $managed_account->id)
			);

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		// Add plan to database
		DB::table('stripe_plans')->insert(
			['id' => $plan->id , 'managed_account_id' => $managed_account->id,
			'job_id' => $job->id, 'activated' => 1]
		);

		if (!$testing) {

			// Queue up subscription job
			dispatch( new StripePlanActivation($this, $job, $plan, $managed_account));

			// Email admin that plan is being created
			//
			Mail::send('emails.admin_job_activating',['job_name'=> $job->title],function($u) use ($job)
			{
			    $u->from('no-reply@jobgrouper.com');
			    $u->to('admin@jobgrouper.com');
			    $u->subject('Job: ' . $job->title .' Is Being Created');
			});
		}

		return $plan;
	}

	public function createPlanInDB($plan_id, $managed_account_id, $job_id) {

		// Add plan to database
		DB::table('stripe_plans')->insert(
			['id' => $plan_id , 'managed_account_id' => $managed_account_id,
			'job_id' => $job_id, 'activated' => 1]
		);
	}

	/*
	 * Generates a unique id for a plan
	 */
	public function generatePlanId($plan_name) {

		return  'plan_' . random_int(10000000, 99999999) . substr( hash('md5', $plan_name), 20);
	}

	public function deletePlan($user, $job, $account_id) {

		$response = NULL;
		$error_response = NULL;

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

		$response = NULL;
		$error_response = NULL;

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

		try {
			$response = Subscription::create(
				array('customer' => $customer_id,
				'plan' => $plan_id,
				'application_fee_percent' => 15,
				'trial_end' => strtotime('+1 day')  // starting a day after so we can modify invoice
			),
				array('stripe_account' => $account_id)
			);

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
			// user error
			$error_response['user'] = true;

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

			// user error (possibly)
			$error_response['user'] = true;

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		// some kind of error mechanism in case this fails
		if ($error_response) {
			return $error_response;
		}
		else {
			$this->createSubscriptionInDB($response['id'], $plan_id, $customer_id);
		}

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

		$response = NULL;
		$error_response = NULL;

		$subscription_record = DB::table('stripe_subscriptions')->where('plan_id', '=', $plan['id'])->
			where('connected_customer_id', '=', $customer['id'])->first();

		$subscription = Subscription::retrieve(array('id' => $subscription_record->id),
			array('stripe_account' => $account_id));

		try {
			$response = $subscription->cancel();

		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		$this->deleteSubscriptionInDB($subscription_record);

		return $response;
	}

	public function deleteSubscriptionInDB($subscription) {
		DB::table('stripe_subscriptions')->where('id', '=', $subscription->id)->delete();
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

		$response = NULL;
		$error_response = NULL;

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

		try {
			// Save invoice
			$invoice->save();

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

		return 1;
	}

	/*
	 * Creates a transfer between a buyer's managed account 
	 *   and it's external account(bank account/debit card).
	 */
	public function createTransfer($user, $job) {

		$response = NULL;
		$error_response = NULL;

		// get the plan
		$account_record = DB::table('stripe_managed_accounts')->
			where('user_id', '=', $user->id)->first();

		try {
			$account = Account::retrieve($account_record->id);

		} catch(\Stripe\Error\Card $e) {
			$error_response = $this->constructErrorResponse($e);
			
		} catch (\Stripe\Error\RateLimit $e) {
		  // Too many requests made to the API too quickly
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\InvalidRequest $e) {
		  // Invalid parameters were supplied to Stripe's API
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Authentication $e) {
		  // Authentication with Stripe's API failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\ApiConnection $e) {
		  // Network communication with Stripe failed
			$error_response = $this->constructErrorResponse($e);

		} catch (\Stripe\Error\Base $e) {
		   // Generic Stripe error
			$error_response = $this->constructErrorResponse($e);

		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
			$error_response = $this->constructErrorResponse($e);

		}

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
