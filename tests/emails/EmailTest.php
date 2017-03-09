<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \Carbon\Carbon;

class EmailTest extends TestCase {

	protected $employee;
	
	public function setUp() {
		parent::setUp();
		$this->user = new StdClass();
		$this->user->id = 1;
		$this->user->email = 'email@email.com';
		$this->user->first_name = 'Jerry';
		$this->user->last_name = 'Goldenbrown';
		$this->user->full_name = 'Jerry Goldenbrown';

		$this->job = new StdClass();
		$this->job->id = 1;
		$this->job->title = 'Test Job';
		$this->job->salary = 50.00;

		$this->order = new StdClass();
		$this->order->id = 1;

		$this->stripeVerificationRequest = new StdClass();
		$this->stripeVerificationRequest->id = 1;
	}

	public function testApplicationNotApprovedEmail() {

		$user = $this->user;

		Mail::send('emails.seller_application_was_not_approved', [], function($u) use ($user)
		{
			$u->from('admin@jobgrouper.com');
			$u->to($user->email);
			$u->subject('Your application was not approved');
		});
	}

	public function testPromptForPaymentMethodEmail() {

		$user = $this->user;

		Mail::send('emails.prompt_for_payment_method', [], function($u) use ($user) {
			$u->from('admin@jobgrouper.com');
			$u->to($user->email);
			$u->subject('You haven\'t set a payment method');
		});
	}

	public function testSellerPaymentSucceededEmail() {

		Mail::send('emails.seller_payment_successful', [], function($u)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to('test@test.com');
		    $u->subject('TEST INVOICE PAID RECEIVED');
		});
	}

	public function testBuyerPaymentSucceededEmail() {

		$buyer = $this->user;
		$employee = $this->user;
		$job = $this->job;

		Mail::send('emails.buyer_payment_successful', ['employee' => $employee->full_name, 'job' => $job], function($u) use ($buyer, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Your payment for '. $job->title . ' has gone through!');
		});
	}

	public function testBuyerJobBegunEmail() {

		$buyer = $this->user;
		$job = $this->job;

		Mail::send('emails.buyer_job_begun',['job_name'=> $job->title ], function($u) use ($buyer, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Job: ' . $job->title . ' has begun');
		});
	}

	public function testSellerJobBegunEmail() {

		$seller = $this->user;
		$job = $this->job;

		Mail::send('emails.seller_job_begun', ['job_name' => $job->title ], function($u) use ($seller, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to( $seller->email );
		    $u->subject('Job: ' . $job->title . ' has begun');
		});
	}

	public function testAdminJobBegunEmail() {

		$job = $this->job;

		Mail::send('emails.admin_job_begun', ['job_name' => $job->title ], function($u) use ($job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to('admin@jobgrouper.com');
		    $u->subject('Job: ' . $job->title . ' has begun');
		});
	}

	public function testBuyerPaymentFailedEmail() {

		$buyer = $this->user;
		$job = $this->job;

		Mail::send('emails.buyer_payment_failed', ['job_name' => $job->title], function($u) use ($buyer, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Your payment for '. $job->title . ' was not accepted.');
		});
	}

	public function testSellerPaymentFailedEmail() {

		$employee = $this->user;
		$job = $this->job;

		Mail::send('emails.seller_payment_failed', [], function($u) use ($employee, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('One of your payments for '. $job->title . ' failed.');
		});
	}

	public function testSellerFullyVerifiedEmail() {

		$employee = $this->user;

		Mail::send('emails.seller_fully_verified', [], function($u) use ($employee)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('You are now fully verified!');
		});
	}

	public function testSellerNeedAdditionalVerificationEmail() {

		$employee = $this->user;
		$stripeVerificationRequest = $this->stripeVerificationRequest;

		Mail::send('emails.seller_need_additional_verification', ['request_id' => $stripeVerificationRequest->id], function($u) use ($employee)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('We need more information to complete your verification');
		});
	}

	public function testBuyerEmployeeApprovedEmail() {

	    $buyer = $this->user;
	    $employee = $this->user;
	    $job = $this->job;
	    $order = $this->order;
	    $parameters = ['employee_name' => $employee->full_name,
				'job_name' => $job->title, 'order_id' => $order->id];

		// Send email to user 
		Mail::send('emails.buyer_employee_approved', $parameters, function($u) use ($buyer)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('The Job ____ is Ready for Purchase');
		});
	}

	public function testConfirmEmail() {

		$token = '1234';
		$user = $this->user;

		Mail::send('emails.confirm',['token'=>$token],function($u) use ($user)
		{
			$u->from('admin@jobgrouper.com');
			$u->to($user->email);
			$u->subject('Confirm Registration');
		});
	}

	public function testSellerConfirmEmail() {

		$user = $this->user;

		Mail::send('emails.seller_confirm', [], function($u) use ($user)
		{
			$u->from('admin@jobgrouper.com');
			$u->to($user->email);
			$u->subject('Final Steps for Verification');
		});
	}

	public function testAdminNewJobApplicationEmail() {

		$job = $this->job;
		$employee = $this->user;

		Mail::send('emails.admin_new_job_application',['job_name'=>$job->title, 
			'employee_name'=>$employee->full_name, 'id' => $employee->id ], function($u) use ($employee, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to('admin@jobgrouper.com');
		    $u->subject('Someone has applied for ' . $job->title);
		});
	}

	public function testEmployeeRequestApprovedEmail() {

		$job = $this->job;
		$employee = $this->user;

		Mail::send('emails.employee_request_approved',['job_name'=>$job->title, 'job_id'=>$job->id],function($u) use ($employee)
		{
			$u->from('admin@jobgrouper.com');
			$u->to($employee->email);
			$u->subject('Request approved!');
		});    
	}

	public function testEmployeeRequestRejectedEmail() {

		$job = $this->job;
		$employee = $this->user;

		Mail::send('emails.employee_request_rejected', ['job_name' => $job->title], function ($u) use ($employee) {
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('Application Status Update');
		});
	}

	public function testEmployeeFiredEmail() {

		$job = $this->job;
		$employee = $this->user;

                Mail::send('emails.discharged_of_work', ['job_name' => $job->title], function ($u) use ($employee, $job) {
                    $u->from('admin@jobgrouper.com');
                    $u->to($employee->email);
                    $u->subject('You have been removed from ' . $job->title);
                });
	}

	public function testBuyerLeavingJobEmail() {

		$job = $this->job;
		$buyer = $this->user;

		Mail::send('emails.buyer_leaving_job', ['buyer' => $buyer, 'job' => $job], function($u)
		{
			$u->from('admin@jobgrouper.com');
			$u->to('admin@jobgrouper.com');
			$u->subject('Buyer leaving the job.');
		});
	}

	public function testBuyerJobOrderedEmailNoEmployee() {

		$job = $this->job;
		$job->employee_id = NULL;
		$user = $this->user;

		Mail::send('emails.buyer_job_ordered', ['job_name' => $job->title, 'employee_exists' => $job->employee_id], function($u) use ($user, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($user->email);
		    $u->subject('Your order for '. $job->title . ' has gone through!');
		});
	}

	public function testBuyerJobOrderedEmailWithEmployee() {

		$job = $this->job;
		$job->employee_id = 2;
		$user = $this->user;

		Mail::send('emails.buyer_job_ordered', ['job_name' => $job->title, 'employee_exists' => $job->employee_id], function($u) use ($user, $job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($user->email);
		    $u->subject('Your order for '. $job->title . ' has gone through!');
		});
	}

	public function testBeginningStripeVerificationEmail() {

		$user = $this->user;

		Mail::send('emails.beginning_stripe_verification', [], function($u) use ($user)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($user->email);
		    $u->subject('We\'ve begun verifying your account');
		});
	}

	public function testEmployeeLeavesJobEmail() {

		$employee = $this->user;
		$buyer = $this->user;
		$job = $this->job;

		Mail::send('emails.employee_leaves_job',['job_name'=>$job->title, 'employee_name'=>$employee->full_name],function($u) use ($buyer)
		{
			$u->from('admin@jobgrouper.com');
			$u->to($buyer->email);
			$u->subject('Employee will leave the work');
		});
	}

	public function testJobWorkStartEmail() {

		$job = $this->job;
		$employee = $this->user;

		//Notify for employee that work starting
		Mail::send('emails.job_work_start',['job_name'=> $job->title, 'job_id'=> $job->id],function($u) use ($employee)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('Work begins');
		});
	}

	public function testAdminJobActivatingEmail() {

		$job = $this->job;
		Mail::send('emails.admin_job_activating',['job_name'=> $job->title],function($u) use ($job)
		{
		    $u->from('admin@jobgrouper.com');
		    $u->to('admin@jobgrouper.com');
		    $u->subject('Job: ' . $job->title .' Is Being Created');
		});
	}
}

?>
