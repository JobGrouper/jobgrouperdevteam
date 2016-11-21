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

		$this->seeInDatabase('stripe_managed_accounts', [
			'id' => $account['id']
		]);

		$this->psi->deleteAccount($account['id']); 
	}
}
