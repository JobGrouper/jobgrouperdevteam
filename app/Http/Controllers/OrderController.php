<?php

namespace App\Http\Controllers;

use DB;

use App\CreditCard;
use App\Job;
use App\Sale;
use Illuminate\Http\Request;
use App\Interfaces\PaymentServiceInterface;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use PayPal\Api\Amount;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;

use Mail;
use Carbon\Carbon;

class OrderController extends Controller
{
    //Это было для сохранения карт и авто-оплат
    /*public function create($job_id){
        $user = Auth::user();

        if($user->orders()->where('job_id', '=', $job_id)->count() > 0){
            return redirect('/job/'.$job_id);
        }

        $credit_cards = $user->credit_cards()->get();
        

        return view('pages.purchase', ['credit_cards' => $credit_cards, 'job_id' => $job_id]);
    }*/


    public function purchase($order_id){
        $user = Auth::user();
        $creditCards = $user->credit_cards()->get();
        $order = Sale::findOrFail($order_id);
        $job = $order->job()->first();
        /*$paymentPayPalData = [
            'payNowButtonUrl' => 'https://www.sandbox.paypal.com/cgi-bin/websc',
            'receiverEmail' => 'ken-facilitator-1@jobgrouper.com',
            'itemName' => 'Payment for order job '.$job->title,
            'amount' => $job->salary,
            'returnUrl' => 'http://jobgrouper.com/api/purchase/paypal/feedback',
            'customData' => ['order_id' => $order->id],
        ];*/


        $changeCard = false;
        return view('pages.purchase', compact('order', /*'paymentPayPalData',*/ 'creditCards', 'job', 'changeCard'));
    }

    public function change_credit_card($order_id){
        $user = Auth::user();
        $creditCards = $user->credit_cards()->get();
        $order = Sale::findOrFail($order_id);
        $job = $order->job()->first();



        $changeCard = true;
        return view('pages.purchase', compact('order', 'job', 'creditCards', 'changeCard'));
    }

    public function set_new_credit_card(Request $request){
        $user = Auth::user();
        $creditCard = CreditCard::find($request->credit_card_id);
        $order = Sale::find($request->order_id);
        $job = $order->job()->first();

        if(!isset($creditCard->id)){
            die('Credit Card not found');
        }

        //Validate card owner
        if($creditCard->owner_id != $user->id){
            die('it is not your card');
        }

        //Assign credit card to order
        $order->credit_card_id = $creditCard->id;

        $order->save();

        return redirect('/my_orders');
    }

    /**
     * Closing the order
     * Originator can be seller ot buyer
     */
    public function close(Request $request, PaymentServiceInterface $psi){
        $responseData= array();

        $order = Sale::find($request->order_id);

        //If job has free place for buyer make it hot
        $job = $order->job()->first();
        if($job->status == 'working'){
            $job->make_hot();
        }

        $seller = $job->employee()->get()->first();

        // Delete Stripe Customer if user signed on
        // while an employee was active
        //
        // (Not completely correct)
        // 	- what about cases where employee leaves?
        // 	- or when there was no employee
        //
        if ($job->employee_id) {

            // get user who is closing order
            //   and managed account
            $user = DB::table('users')->where('id', $order->buyer_id)->first();
            $seller_record = DB::table('stripe_managed_accounts')->where('user_id', $job->employee_id)->first();

	    // Cancel subscription
	    // 	Must be subscribed if sale in progress and job is working
	    //
	    if ($order->status == 'in_progress' && $job->status == 'working') {
		    $plan = $psi->retrievePlan($job, $seller_record->id);
		    $customer = $psi->retrieveCustomerFromUser($user, $job, $seller_record->id);
		    $subscription_response = $psi->cancelSubscription($plan, $customer, $seller_record->id);
	    }

            // Delete payment service records
            $response = $psi->deleteCustomer($user, $job, $seller_record->id);

            //mail to seller
            Mail::send('emails.buyer_leaving_job', ['buyer' => Auth::user(), 'job' => $job], function($u)
            {
                $u->from('admin@jobgrouper.com');
                $u->to('admin@jobgrouper.com');
                $u->subject('Buyer leaving the job.');
            });

            //mail to admin
            Mail::send('emails.buyer_leaving_job', ['buyer' => Auth::user(), 'job' => $job], function($u) use ($seller)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($seller->email);
                $u->subject('Buyer leaving the job.');
            });
        }

        $order->status = 'closed';
        $order->save();

        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Order successfully closed';

        return response($responseData, 200);

    }



    public function store(Request $request, PaymentServiceInterface $psi){
        $user = Auth::user();
        //Это было для сохранения карт и авто-оплат
        //$input = $request->only(['credit_card_id', 'job_id']);

        /*if(!($user->credit_cards()->where('id', '=', $input['credit_card_id'])->count() > 0)){
            die('it is not your card');
        }*/


        $input = $request->only(['job_id']);
        $job = Job::find($input['job_id']);

	$input['status'] = 'pending';

        $order = $user->orders()->create($input);


	// Send email to user 
	Mail::send('emails.buyer_job_ordered', ['job_name' => $job->title, 'employee_exists' => $job->employee_id], function($u) use ($user, $job)
	{
	    $u->from('admin@jobgrouper.com');
	    $u->to($user->email);
	    $u->subject('Your order for '. $job->title . ' has gone through!');
	});

        //if card has enough count of buyers and sellers the work begins
        if($job->sales_count == $job->max_clients_count && null != $job->employee_id){
            //$job->work_start();
        }

        if(isset($order->id)){
            return redirect('/my_orders');
            //Это было для сохранения карт и авто-оплат
            /*Session::flash('message', 'Order has been successfully created!');
            return redirect('/purchase/'.$input['job_id']);*/
        }

    }


    public function update(Request $request, PaymentServiceInterface $psi){

	$this->validate($request, [
		    'card_number' => 'required',
		    'cvc' => 'required',
		    'exp_month' => 'required|integer',
		    'exp_year' => 'required|size:4'
		    ]);

        $user = Auth::user();
        //$creditCard = CreditCard::find($request->credit_card_id);
        $order = Sale::find($request->order_id);
        $job = $order->job()->first();

        if($job->sales_count >= $job->max_clients_count){
		// redirect
		return redirect('purchase/' . $order->id)->
			withErrors([ 'Cannot confirm order, the maximum amount of buyers has been reached' ]);
	}

	// Retrieve employee account
	$employee_record = DB::table('stripe_managed_accounts')->where(
		'user_id', '=', $job->employee_id)->first();

	$account = $psi->retrieveAccount($employee_record->id);

	// Create credit card token
	$token = $psi->createCreditCardToken(
		array(
			'number' => $request->card_number,
			'exp_month' => $request->exp_month,
			'exp_year' => $request->exp_year,
			'cvc' => $request->cvc
		), 'card', true);

	if (!isset($token['id'])) {

		// redirect
		return redirect('purchase/' . $order->id)->
			withErrors([ $token['message'] ]);
	}

	// Create customer
	$customer = $psi->createCustomer($user, $job, array(
		'email' => $user->email), $account['id']);

	if (!isset($customer['id'])) {

		// redirect
		return redirect('purchase/' . $order->id)->
			withErrors(['Server error. Try again later.']);
	}

	$source = $psi->updateCustomerSource($user, $token, $account['id']);
	
	if (isset($source['id'])) {
	  $order->status = 'in_progress';
	  $order->card_set = True;
          $order->save();
	}
	else {
	  die('Payment saving failed!');
	} 
	
	if ($job->sales_count >= $job->min_clients_count && $job->employee_id != null) {

		//if card has enough count of buyers and sellers the work begins
		if($job->status != 'working'){

			$employee = $job->employee()->first();
			$psi->createPlan($employee, $job);

			// End all early bird buyers

		}
		else {

			$employee = $job->employee()->first();
			$seller_account = $psi->retrieveAccountFromUser($employee);
			$plan = $psi->retrievePlan($job, $seller_account['id']);
			$response = $psi->createSubscription($plan, $customer, $seller_account);
		}
	}

	Mail::queue('emails.buyer_order_confirmed', ['job' => $job], function($u) use ($user) {
		$u->from('admin@jobgrouper.com');
		$u->to($user->email);
		$u->subject('Order confirmed!');
	});

	/*
        if(!isset($creditCard->id)){
            die('Credit Card not found');
        }

        //Validate card owner
        if($creditCard->owner_id != $user->id){
            die('it is not your card');
        }
	 */



        //Assign credit card to order
        //$order->credit_card_id = $creditCard->id;

	/*
        //Get payment for first month
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),     // ClientID
                env('PAYPAL_CLIENT_SECRET')      // ClientSecret
            )
        );

	$apiContext->setConfig(array('mode' => env('PAYPAL_API_MODE')));

        $creditCardToken = new CreditCardToken();
        $creditCardToken->setCreditCardId($creditCard->card_id);

        $fi = new FundingInstrument();
        $fi->setCreditCardToken($creditCardToken);

        $payer = new Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));


        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($job->monthly_price);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription("Payment for first month")
            ->setInvoiceNumber(uniqid());

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));

        try {
            $payment->create($apiContext);
        } catch (\Exception $ex) {
            dd($ex);
            //dd(json_decode($ex->getData()->me, true));
            //Session::flash('message', json_decode($ex->getData())->message);
            //return redirect('/card/create');
        }

        $payment = $order->payments()->create([
            'buyer_id' => $user->id,
            'amount' => $job->monthly_price,
            'month' => $order->month_to_pay,
            'payment_system' => 'paypal',
            'status' => 'success',
        ]);

        if(!isset($payment->id)){
            die('Payment saving failed!');
        }

	 */

        return redirect('/my_orders');
    }
}
