<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;

use \App\PaymentServices\StripeService;
use App\Job;
use App\User;

class StripeIntegrationTest extends TestCase
{
	use DatabaseTransactions;

	protected $psi;

	public function setUp() {
		parent::setUp();
		$this->psi = new StripeService();
	}

	public function testInterfaceCreated() {

		$this->assertInstanceOf('\App\PaymentServices\StripeService', $this->psi);
	}

	public function testCreateAccount() {

		$this->markTestSkipped();
		$account = $this->psi->createAccount(array(
			"country" => "US",
			"email" => "testemail@test.com",
			"legal_entity" => array(
				"address" => array(
					"city" => "Malibu",
					"line1" => "line",
					"postal_code" => "90210",
					"state" => "CA"),
				"dob" => array(
					"day" => "1",
					"month" => "2",
					"year" => "1986"
				),
				"first_name" => "Test",
				"last_name" => "User",
				"ssn_last_4" => "9999",
				"type" => "individual"
			),
			"tos_acceptance" => array(
				"date" => Carbon::now()->timestamp,
				"ip" => "8.8.8.8"
			)
		), 1, True);

		$this->assertArrayHasKey('id', $account);

		$this->psi->deleteAccount($account['id']); 
	}

	public function testCreateAccountInDB() {

		$this->markTestSkipped();
		$account = array(
			'id' => 1
		);

		$this->psi->insertAccountIntoDB($account['id'], 1);

		$this->seeInDatabase('stripe_managed_accounts', [
			'id' => $account['id']
		]);

		$this->psi->deleteAccountFromDB($account['id']); 
	}

	public function testCreateCreditCardToken() {

		$this->markTestSkipped();
		$token = $this->psi->createCreditCardToken(array(
			'number' => "4242424242424242",
			'exp_month' => 11,
			'exp_year' => 2017,
			'cvc' => "314"
		));

		$this->assertArrayHasKey('id', $token);
	}

	public function testCreateCustomerForRegistration() {

		$this->markTestSkipped();
		$user = new StdClass();
		$user->id = 1;
		$user->email = 'testmail@test.com';

		$token = $this->psi->createCreditCardToken(array(
			'number' => "4242424242424242",
			'exp_month' => 11,
			'exp_year' => 2017,
			'cvc' => "314"
		));

		$this->psi->createCustomer($user, array(
			'email' => $user->email,
			'source' => $token['id'])
		);

		$this->seeInDatabase('stripe_root_customers',
			['user_id' => $user->id]
		);	

		$this->psi->deleteCustomer($user);
	}

	public function testCreateCustomerForSubscription() {

		$this->markTestSkipped();
		$user = new StdClass();
		$user->id = 1;
		$user->email = 'testmail@test.com';

		$account_id = 'acct_198BveHmuu0N2CC4';

		// insert account into db
		$this->psi->insertAccountIntoDB($account_id, 1);

		$this->psi->createCustomer($user, array(
			'email' => $user->email)
		);

		$this->psi->createCustomer($user, array(
			'email' => $user->email),
			$account_id
		);

		$this->seeInDatabase('stripe_connected_customers',
			['user_id' => $user->id,
			'managed_account_id' => $account_id]
		);	

		$this->psi->deleteCustomer($user, $account_id);
		$this->psi->deleteCustomer($user);

		$this->psi->deleteAccountFromDB($account_id); 
	}

	public function testGeneratePlanId() {

		$this->markTestSkipped();
		$plan_name = 'Test Plan';
		$plan_id = $this->psi->generatePlanId($plan_name);
		$this->assertEquals(25, strlen($plan_id));
	}

	public function testCreatePlan() {

		$this->markTestSkipped();
		// Create a fake-oh job
		 $job = Job::create([
		    'title' => 'Test Job',
		    'description' => "A job for testing",
		    'salary' => 50.00,
		    'max_clients_count' => 5,
		    'category_id' => 1,
		]);

		// Create a fake user
		$user = new StdClass();
		$user->id = 1;

		DB::statement("SET FOREIGN_KEY_CHECKS=0");

		// Create a fake account for the fake user
		// 	- will not be done in live situation
		$account = $this->psi->createAccount(array(
			"country" => "US",
			"email" => "testemail@test.com",
			"legal_entity" => array(
				"address" => array(
					"city" => "Malibu",
					"line1" => "line",
					"postal_code" => "90210",
					"state" => "CA"),
				"dob" => array(
					"day" => "1",
					"month" => "2",
					"year" => "1986"
				),
				"first_name" => "Test",
				"last_name" => "User",
				"ssn_last_4" => "9999",
				"type" => "individual"
			),
			"tos_acceptance" => array(
				"date" => Carbon::now()->timestamp,
				"ip" => "8.8.8.8"
			)
		), $user->id, True);

		$plan = $this->psi->createPlan($user, $job, True);

		$this->seeInDatabase('stripe_plans',
			['id' => $plan['id']]
		);

		// DESTROY EVERYTHING
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
		$this->psi->deletePlan($user, $job, $account['id']);
		$this->psi->deleteAccount($account['id']);
		$job->delete();
	}

	/*
	 * This is very messy, I apologize
	 */
	public function testCreateSubscription() {

		$this->markTestSkipped();
		// Create a fake-oh job
		 $job = Job::create([
		    'title' => 'Test Job',
		    'description' => "A job for testing",
		    'salary' => 50.00,
		    'max_clients_count' => 5,
		    'category_id' => 1,
		]);

		// Create a fake user (customer)
		$buyer = new StdClass();
		$buyer->id = 2;
		$buyer->email = 'testbuyer@test.com';

		DB::statement("SET FOREIGN_KEY_CHECKS=0");

		// Create a fake account for the fake user
		// 	- will not be done in live situation
		$seller = new StdClass();
		$seller->id = 1;

		$account = $this->psi->createAccount(array(
			"country" => "US",
			"email" => "testemail@test.com",
			"legal_entity" => array(
				"address" => array(
					"city" => "Malibu",
					"line1" => "line",
					"postal_code" => "90210",
					"state" => "CA"),
				"dob" => array(
					"day" => "1",
					"month" => "2",
					"year" => "1986"
				),
				"first_name" => "Test",
				"last_name" => "User",
				"ssn_last_4" => "9999",
				"type" => "individual"
			),
			"tos_acceptance" => array(
				"date" => Carbon::now()->timestamp,
				"ip" => "8.8.8.8"
			)
		), $seller->id, True);


		// Create a fake customer for the fake plan and the fake everything
		//
		// // starting with the root
		$root = $this->psi->createCustomer($buyer, array(
			'email' => $buyer->email)
		);

		// and then the real (fake) deal
		$customer = $this->psi->createCustomer($buyer, array(
			'email' => $buyer->email),
			$account['id']
		);

		$plan = $this->psi->createPlan($seller, $job, True);

		$subscription = $this->psi->createSubscription($plan, $customer, $account);

		$this->seeInDatabase('stripe_subscriptions',
			['id' => $subscription['id']]
		);

		// DESTROY ALL
		DB::statement("SET FOREIGN_KEY_CHECKS=1");
		$this->psi->cancelSubscription($plan, $customer, $account['id']);
		$this->psi->deletePlan($seller, $job, $account['id']);
		$this->psi->deleteCustomer($buyer, $account['id']);
		$this->psi->deleteCustomer($buyer);
		$this->psi->deleteAccount($account['id']); 
		$job->delete();
	}

	/*
	 * Create external account is likely to receive a token and a user id
	 */
	public function testCreateExternalAccount() {

		//Create fake user
		$user = User::create([
		    'first_name' => 'Teddy',
		    'last_name' => 'Thanopoklos',
		    'user_type' => 'buyer',
		    'email' => 'teddy1@bearmail.com',
		    'password' => bcrypt('password'),
		]);

		$account = $this->psi->createAccount(array(
			"country" => "US",
			"email" => "testemail@test.com",
			"legal_entity" => array(
				"address" => array(
					"city" => "Malibu",
					"line1" => "line",
					"postal_code" => "90210",
					"state" => "CA"),
				"dob" => array(
					"day" => "1",
					"month" => "2",
					"year" => "1986"
				),
				"first_name" => "Test",
				"last_name" => "User",
				"ssn_last_4" => "9999",
				"type" => "individual"
			),
			"tos_acceptance" => array(
				"date" => Carbon::now()->timestamp,
				"ip" => "8.8.8.8"
			)
		), $user->id, True);

		$token = $this->psi->createCreditCardToken(array(
			'number' => "4000056655665556",
			'exp_month' => 11,
			'exp_year' => 2017,
			'cvc' => "314"
		), True);

		$card = $this->psi->createExternalAccount($user, $token);

		$this->seeInDatabase('stripe_external_accounts',
			['id' => $card['id'], 'managed_account_id' => $account['id']]);

		// DELETE ALL
		try {
			
			// This will fail without a replacement card,
			// 	we need to account for this in our logic .!.!
			//
		  	$this->psi->deleteExternalAccount($user->id, $card['id']);

			$this->psi->deleteAccount($account['id']);
			$user->delete();
		}
		catch(Exception $e) {

			$this->psi->deleteExternalAccountInDB($card['id']);
			$this->psi->deleteAccount($account['id']);
			$user->delete();
		}
	}

	public function testUpdateCustomerSource() {
		$this->markTestSkipped();
	}

	public function testCreateTransfer() {
		$this->markTestSkipped();
	}
}
