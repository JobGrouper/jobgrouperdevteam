<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\PaymentServiceInterface;

use App\Http\Requests;

class StripeWebhookController extends Controller
{
    //
	//
	public function onInvoicePaid(PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input);

		Log::info('Invoice paid webhook received', ['event' => $event_json]);
	}

	public function onInvoiceCreated(PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input);

		Log::info('Invoice created webhook received', ['event' => $event_json]);
	}

	public function onInvoiceFailure(PaymentServiceInterface $psi) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input);

		Log::info('Invoice failure webhook received', ['event' => $event_json]);
	}

	public function onAccountUpdated(PaymentServiceInterface $psi) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input);

		Log::info('Account updated webhook received', ['event' => $event_json]);
	}
}
