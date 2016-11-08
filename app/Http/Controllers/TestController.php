<?php

namespace App\Http\Controllers;

use App\PaymentServices\StripeService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Mail;
use \Stripe\Stripe;
use \Stripe\Charge;
use \Stripe\Token;
use \Stripe\Customer;

class TestController extends Controller
{
    public function test(){
        $creditCardData = [
        'number' => '4111111111111111',
        'exp_month' => 12,
        'exp_year' => 20,
        'cvc' => 123,

        ];
        $stripeService = new StripeService();
        $creditCardToken = $stripeService->createCreditCardToken($creditCardData);

        dd($creditCardToken);
    }
}
