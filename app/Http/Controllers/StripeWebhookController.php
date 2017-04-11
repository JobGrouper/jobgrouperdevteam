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
use App\Jobs\StripeTransferCreated;
use App\Jobs\StripeTransferUpdated;
use App\Jobs\StripeTransferPaid;
use App\Jobs\StripeTransferFailed;

class StripeWebhookController extends Controller
{
    //
	//
	public function onInvoicePaid(Request $request) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		dispatch( new StripeInvoicePaid($event_json) );

		return response('Successful', 200);
	}

	public function onInvoiceCreated(Request $request, PaymentServiceInterface $psi) {

		// retrieve the request's body and parse it as json
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		//Log::info('Invoice created webhook received', ['event' => $event_json]);

		return response('Successful', 200);
	}

	public function onInvoiceFailure(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");
		$event_json = json_decode($input, true);

		dispatch( new StripeInvoiceFailed($event_json) );

		return response('Successful', 200);
	}

	public function onAccountUpdated(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripeAccountUpdated($event_json) );

		return response('Successful', 200);
	}

	public function onTransferCreated(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		var_dump($input);

		$event_json = $this->inputOrRequest($request, $input);

		var_dump($event_json);

		dispatch( new StripeTransferCreated($event_json) );

		return response('Successful', 200);
	}

	public function onTransferPaid(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripeTransferPaid($event_json) );

		return response('Successful', 200);
	}

	public function onTransferFailed(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripeTransferFailed($event_json) );

		return response('Successful', 200);
	}

	public function onTransferUpdated(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripeTransferUpdated($event_json) );

		return response('Successful', 200);
	}

	private function inputOrRequest($request, $input, $name=NULL) {

		if ($input == "") {
			return $request->all();
		}
		else {
			return json_decode($input, true);
		}
	}
}
