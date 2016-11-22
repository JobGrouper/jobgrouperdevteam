<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;

use \App\PaymentServices\StripeService;

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

		$token = $this->psi->createCreditCardToken(array(
			'number' => "4242424242424242",
			'exp_month' => 11,
			'exp_year' => 2017,
			'cvc' => "314"
		));

		$this->assertArrayHasKey('id', $token);
	}

	public function testCreateCustomerForRegistration() {

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
}
