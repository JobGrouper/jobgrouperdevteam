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
use App\Jobs\StripePayoutCreated;
use App\Jobs\StripePayoutUpdated;
use App\Jobs\StripePayoutPaid;
use App\Jobs\StripePayoutFailed;

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

	public function onPayoutCreated(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		var_dump($input);

		$event_json = $this->inputOrRequest($request, $input);

		var_dump($event_json);

		dispatch( new StripePayoutCreated($event_json) );

		return response('Successful', 200);
	}

	public function onPayoutPaid(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripePayoutPaid($event_json) );

		return response('Successful', 200);
	}

	public function onPayoutFailed(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripePayoutFailed($event_json) );

		return response('Successful', 200);
	}

	public function onPayoutUpdated(Request $request) {

		// Retrieve the request's body and parse it as JSON
		$input = @file_get_contents("php://input");

		$event_json = $this->inputOrRequest($request, $input);

		dispatch( new StripePayoutUpdated($event_json) );

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
