<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;
use App\Operations\StartNewEarlyBirdOP;

use \App\User;
use \App\Job;

use App\Interfaces\PaymentServiceInterface;
use App\PaymentServices\StripeService;
use \Stripe\Customer;
use \Stripe\Stripe;
use \Stripe\Invoice;
use \Stripe\Token;
use \Stripe\Plan;
use \Stripe\Subscription;

class StartNewEarlyBirdOPTest extends TestCase {

	use DatabaseTransactions;

	protected $account_id = 'acct_1AJLqbBbpkWPXbD0';
	protected $customer_id = NULL;
	protected $customer;
	protected $plan_id = NULL;
	protected $plan;

	public function setUp() {

		parent::setUp();
		$this->op = \App::make('App\Operations\StartNewEarlyBirdOP');
	}

	public function tearDown() {

		// Delete Customer
		if ($this->customer_id != NULL) {

			$this->customer_id = NULL;
			$this->customer->delete();
			$this->customer = NULL;
		}

		if ($this->plan_id != NULL) {

			$this->plan_id = NULL;

			if ($this->plan) {
				$this->plan->delete();
				$this->plan = NULL;
			}
		}
	}

	public function testConstruct() {

		$this->assertInstanceOf(StartNewEarlyBirdOP::class, $this->op);
	}

	public function testGo() {

		$this->markTestSkipped();
		// create User:Employee
		//
		$employee = User::create([
		    'first_name' => 'Hello',
		    'last_name' => 'There',
		    'user_type' => 'employee',
		    'email' => '1@test.com',
		    'password' => bcrypt('password')
		]);

		$employee->managed_account_id = $this->account_id;

		// Link User:Employee with Stripe Managed Account
		DB::table('stripe_managed_accounts')->insert([
			'id' => $this->account_id,
			'user_id' => $employee->id
		]);

		// create User:Buyer
		$buyer = User::create([
		    'first_name' => 'Buyer',
		    'last_name' => 'There',
		    'user_type' => 'buyer',
		    'email' => '2@test.com',
		    'password' => bcrypt('password')
		]);

		// create Token for Customer
		$token = Token::create(
			array( "card" => array(
					"number" => "4242424242424242",
					"exp_month" => 11,
					"exp_year" => 2020,
					"cvc" => 123,
					"currency" => 'USD'
					)
				)
			);

		// Create Customer
		$customer_object = Customer::create(
			array('description' => 'Testing Employee Exit Requests',
				'source' => $token->id),
			array('stripe_account' => $this->account_id));

		// assign customer variables for teardown
		$this->customer_id = $customer_object->id;
		$this->customer = $customer_object;

		// create Job
		$job = Job::create([
		    'title' => 'Test Job',
		    'description' => "A job for testing",
		    'salary' => 50.00,
		    'max_clients_count' => 5,
		    'min_clients_count' => 4,
		    'category_id' => 1,
		]);

		// set job's employee
		$job->employee_id = $employee->id;
		$job->save();

		// Create an Order
		$order = $buyer->orders()->create(
			['job_id' => $job->id]);

		$order->status = 'in_progress';
		$order->save();

		// Link User:Buyer with Stripe Managed Account and Connected Customer
		//
		DB::table('stripe_connected_customers')->insert([
			'id' => $this->customer_id,
			'user_id' => $buyer->id,
			'managed_account_id' => $this->account_id,
			'job_id' => $job->id
		]);

		$early_bird = $buyer->early_bird_buyers()->create([
			'user_id' => $buyer->id,
			'employee_id' => $employee->id,
			'job_id' => $job->id,
			'sale_id' => $order->id,
			'status' => 'requested']);

		$this->op->go($job, $early_bird);
	}

}

?>
