<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\PaymentServiceInterface;

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

		echo $account_id;
		echo '<br>';
		echo $customer_id;

		return response('Successful', 200);
	}

	public function onInvoiceCreated(PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		Log::info('Invoice created webhook received', ['event' => $event_json]);

		return response('Successful', 200);
	}

	public function onInvoiceFailure(PaymentServiceInterface $psi) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Invoice failure webhook received', ['event' => $event_json]);

		$account_id = $event_json['user_id'];
		$customer_id = $event_json['data']['object']['customer'];
		$description = $event_json['data']['object']['description'];

		echo $account_id;
		echo '<br>';
		echo $customer_id;
		echo 'Description: ' . $description;

		return response('Successful', 200);
	}

	public function onAccountUpdated(PaymentServiceInterface $psi) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Account updated webhook received', ['event' => $event_json]);

		$account_id = $event_json['data']['object']['id'];
		$verified = $event_json['data']['object']['legal_entity']['verification']['status'];
		$verification = $event_json['data']['object']['verification']['disabled_reason'];
		$fields_needed = $event_json['data']['object']['verification']['fields_needed'];

		echo $account_id;
		echo "||";
		echo $verified;
		echo "||";
		echo $verification;
		echo "||";
		var_dump($fields_needed);

		return response('Successful', 200);
	}
}
