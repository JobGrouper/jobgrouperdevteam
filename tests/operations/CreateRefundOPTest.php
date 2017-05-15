<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;
use \App\Operations\CreateRefundOP;

use \App\User;
use \App\Job;

use App\Interfaces\PaymentServiceInterface;
use App\PaymentServices\StripeService;
use \Stripe\Customer;
use \Stripe\Stripe;
use \Stripe\Invoice;

class CreateRefundOPTest extends TestCase {

	use DatabaseTransactions;

	protected $account_id = 'acct_1AJLqbBbpkWPXbD0';
	protected $customer_id = 'cus_AefIeUICCPN44n';
	protected $plan_id = 'plan_00000000TEST';
	protected $subscription_id = 'sub_AefJ6PP7Uv9fef';

	public function setUp() {

		parent::setUp();
		$this->op = \App::make('App\Operations\CreateRefundOP');
	}

	public function testConstruct() {

		$this->assertInstanceOf(CreateRefundOP::class, $this->op);
	}

	public function testGo() {

		/*
		Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
			  $response = Customer::retrieve(array('id' => $this->customer_id),
					array('stripe_account' => $this->account_id));
		$psi = new StripeService();
		$customer = $psi->retrieveCustomer($this->customer_id, $this->account_id);
		 */

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

		// create Job
		$job = Job::create([
		    'title' => 'Test Job',
		    'description' => "A job for testing",
		    'salary' => 50.00,
		    'max_clients_count' => 5,
		    'category_id' => 1,
		]);

		// Link User:Buyer with Stripe Managed Account and Connected Customer
		//
		DB::table('stripe_connected_customers')->insert([
			'id' => $this->customer_id,
			'user_id' => $buyer->id,
			'managed_account_id' => $this->account_id,
			'job_id' => $job->id
		]);

		// Link Job with Plan from Stripe Managed Account and Connected Customer
		DB::table('stripe_plans')->insert([
			'id' => $this->plan_id,
			'managed_account_id' => $this->account_id,
			'job_id' => $job->id,
			'activated' => 1
			]);
		
		// Link Subscription
		//   which will create invoice
		//   which will create a charge...maybe
		DB::table('stripe_subscriptions')->insert([
			'id' => $this->subscription_id,
			'plan_id' => $this->plan_id,
			'connected_customer_id' => $this->customer_id,
			'activated' => 1
			]);

		// Create new invoice
		/*
		$new_invoice = Invoice::create(array('customer' => $this->customer_id,
					'subscription' => $this->subscription_id),
			array('stripe_account' => $this->account_id));
		 */

		$refund = $this->op->go($employee, $buyer);
	}
}
