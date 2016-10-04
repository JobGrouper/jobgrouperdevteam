<?php

namespace App\Http\Controllers;

use App\CreditCard;
use App\Job;
use App\Sale;
use Illuminate\Http\Request;

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
    public function close(Request $request){
        $responseData= array();

        $order = Sale::find($request->order_id);
        $order->status = 'closed';
        $order->save();

        //If job has free place for buyer make it hot
        $job = $order->job()->first();
        if($job->status == 'working'){
            $job->make_hot();
        }

        $responseData['error'] = false;
        $responseData['status'] = 0;
        $responseData['info'] = 'Order successfully closed';

        return response($responseData, 200);

    }



    public function store(Request $request){
        $user = Auth::user();
        //Это было для сохранения карт и авто-оплат
        //$input = $request->only(['credit_card_id', 'job_id']);

        /*if(!($user->credit_cards()->where('id', '=', $input['credit_card_id'])->count() > 0)){
            die('it is not your card');
        }*/


        $input = $request->only(['job_id']);
        $job = Job::find($input['job_id']);

        $order = $user->orders()->create($input);

        //if card has enough count of buyers and sellers the work begins
        if($job->sales_count == $job->max_clients_count && null != $job->employee_id){
            $job->work_start();
        }



        if(isset($order->id)){
            return redirect('/my_orders');
            //Это было для сохранения карт и авто-оплат
            /*Session::flash('message', 'Order has been successfully created!');
            return redirect('/purchase/'.$input['job_id']);*/
        }

    }


    public function update(Request $request){
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

        //Get payment for first month
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),     // ClientID
                env('PAYPAL_CLIENT_SECRET')      // ClientSecret
            )
        );

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

        $order->save();

        return redirect('/my_orders');
    }
}
