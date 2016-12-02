<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\PaymentServiceInterface;

use DB;
use Mail;

use App\Http\Requests;

class StripeWebhookController extends Controller
{
    //
	//
	public function onInvoicePaid(PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Invoice paid webhook received', ['event' => $event_json]);

		$account_id = $event_json['user_id'];
		$customer_id = $event_json['data']['object']['customer'];
		$plan_id = $event_json['data']['object']['lines']['data'][0]['plan']['id'];

		// GET BUYER AND SELLER
		//
		// -- make this into a join later
		//
		$buyer_record = DB::table('stripe_connected_customers')->
			where('id', $customer_id)->
			where('managed_account_id', $account_id)->first();

		$buyer = DB::table('users')->
			where('id', $buyer_record->user_id)->first();

		// Get employee
		$employee_record = DB::table('stripe_managed_accounts')->
			where('id', $account_id)->first();

		$employee = DB::table('users')->
			where('id', $employee_record->user_id)->first();

		// Get plan
		$plan_record = DB::table('stripe_plans')->
			where('id', $plan_id)->first();

        	$job = Job::find($plan_record->job_id);


		Mail::send('emails.buyer_payment_successful', ['employee' => $employee->full_name(), 'job_name' => $job->title], function($u)
		{
		    $u->from('no-reply@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Your payment for '. $job->title . ' has gone through!');
		});
		
		Mail::send('emails.seller_payment_successful', [], function($u)
		{
		    $u->from('no-reply@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('A payment for '. $job->title . ' has gone through!');
		});

		return response('Successful', 200);
	}

	public function onInvoiceCreated(PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Invoice created webhook received', ['event' => $event_json]);

		return response('Successful', 200);
	}

	public function onInvoiceFailure(PaymentServiceInterface $psi) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Invoice failure webhook received', ['event' => $event_json]);

		$account_id = $event_json['user_id'];
		$customer_id = $event_json['data']['object']['customer'];
		$plan_id = $event_json['data']['object']['lines']['data'][0]['plan']['id'];
		$description = $event_json['data']['object']['description'];

		// GET BUYER AND SELLER
		//
		// -- make this into a join later
		//
		$buyer_record = DB::table('stripe_connected_customers')->
			where('id', $customer_id)->
			where('managed_account_id', $account_id)->first();

		$buyer = DB::table('users')->
			where('id', $buyer_record->user_id)->first();

		// Get employee
		$employee_record = DB::table('stripe_managed_accounts')->
			where('id', $account_id)->first();

		$employee = DB::table('users')->
			where('id', $employee_record->user_id)->first();

		// Get plan
		$plan_record = DB::table('stripe_plans')->
			where('id', $plan_id)->first();

        	$job = Job::find($plan_record->job_id);


		Mail::send('emails.buyer_payment_failed', ['job_name' => $job->title], function($u)
		{
		    $u->from('no-reply@jobgrouper.com');
		    $u->to($buyer->email);
		    $u->subject('Your payment for '. $job->title . ' was not accepted.');
		});
		
		Mail::send('emails.seller_payment_failed', [], function($u)
		{
		    $u->from('no-reply@jobgrouper.com');
		    $u->to($employee->email);
		    $u->subject('One of your payments for '. $job->title . ' failed.');
		});

		return response('Successful', 200);
	}

	public function onAccountUpdated(PaymentServiceInterface $psi) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Account updated webhook received', ['event' => $event_json]);

		$account_id = $event_json['data']['object']['id'];
		$verification_status = $event_json['data']['object']['legal_entity']['verification']['status'];
		$verification = $event_json['data']['object']['verification']['disabled_reason'];
		$fields_needed = $event_json['data']['object']['verification']['fields_needed'];

		// Get employee
		$employee_record = DB::table('stripe_managed_accounts')->
			where('id', $account_id)->first();

		$employee = DB::table('users')->
			where('id', $employee_record->user_id)->first();

		if ($verification_status == 'verified') {

			Mail::send('emails.seller_fully_verified', [], function($u)
			{
			    $u->from('no-reply@jobgrouper.com');
			    $u->to($employee->email);
			    $u->subject('You\'re fully verified on JobGrouper!');
			});
		}
		else {

			Mail::send('emails.seller_need_additional_verification', ['id' => $employee->id], function($u)
			{
			    $u->from('no-reply@jobgrouper.com');
			    $u->to($employee->email);
			    $u->subject('You\'re fully verified on JobGrouper!');
			});
		}

		return response('Successful', 200);
	}
}
