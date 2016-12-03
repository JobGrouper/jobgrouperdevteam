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
        $stripeService = new StripeService();
        /*$res = $stripeService->createAccount([
            'email' => 'testmail1@test.mail',
            'country' => 'us',
        ]);

        dd($res);*/

        $res = $stripeService->updateAccount('acct_19EfY8EyRcaduvX2', [
            'legal_entity' => [
                'dob' => [
                    'day' => 5,
                    'month' => 5,
                    'year' => 1995,
                ]
            ]
        ]);

        dd($res);
    }
}
