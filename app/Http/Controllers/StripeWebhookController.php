<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Interfaces\PaymentServiceInterface;

use DB;
use Mail;

use App\Http\Requests;

use App\Jobs\StripeAccountUpdated;
use App\Jobs\StripeInvoiceFailed;
use App\Jobs\StripeInvoicePaid;

class StripeWebhookController extends Controller
{
    //
	//
	public function onInvoicePaid() {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		dispatch( new StripeInvoicePaid($event_json) );

		return response('Successful', 200);
	}

	public function onInvoiceCreated(PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Invoice created webhook received', ['event' => $event_json]);

		return response('Successful', 200);
	}

	public function onInvoiceFailure() {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		dispatch( new StripeInvoiceFailed($event_json) );

		return response('Successful', 200);
	}

	public function onAccountUpdated() {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		dispatch( new StripeAccountUpdated($event_json) );

		return response('Successful', 200);
	}
}
