<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use PayPal\Api\Amount;
use PayPal\Api\CreditCard;
use PayPal\Api\Details;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use URL;

class CreditCardController extends Controller
{
    public function create(){
        Session::flash('continuePurchaseUrl', URL::previous()); //To redirect user to purchase page after card creation
        return view('pages.account.card');
    }
    
    public function store(Request $request){
        $user = Auth::user();
        $input = $request->only(['first_name', 'last_name', 'card_number', 'end_month', 'end_year', 'cvv']);

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),     // ClientID
                env('PAYPAL_CLIENT_SECRET')      // ClientSecret
            )
        );

	$apiContext->setConfig(array('mode' => env('PAYPAL_API_MODE')));

        $card = new CreditCard();
        $card ->setType($this->cardType($input['card_number']))
            ->setNumber($input['card_number'])
            ->setExpireMonth($input['end_month'])
            ->setExpireYear($input['end_year'])
            ->setCvv2($input['cvv'])
            ->setFirstName($input['first_name'])
            ->setLastName($input['last_name']);


        try {
            $card->create($apiContext);
        } catch (\Exception $ex) {
            dd($ex);
            //dd(json_decode($ex->getData()->me, true));
            Session::flash('message_error', json_decode($ex->getData())->message);
            return redirect('/card/create');
        }


        $card = $user->credit_cards()->create([
            'card_id' => $card->id,
            'valid_until' => $card->valid_until,
            'type' => $card->type,
            'number' => $card->number,
            'expire_month' => $card->expire_month,
            'expire_year' => $card->expire_year,
            'first_name' => $card->first_name,
            'last_name' => $card->last_name
        ]);

        if(isset($card->id)){
            if(Session::get('continuePurchaseUrl')){
                return redirect(Session::get('continuePurchaseUrl'));
            }
            else{
                Session::flash('message_success', 'Card has been successfully added to your account!');
                return redirect('/card/create');
            }
        }
    }


    /**
     * Return credit card type if number is valid
     * @return string
     * @param $number string
     **/
    private function cardType($number)
    {
        $number=preg_replace('/[^\d]/','',$number);
        if (preg_match('/^3[47][0-9]{13}$/',$number))
        {
            return 'amex';
        }
        elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
        {
            return 'Diners Club';
        }
        elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
        {
            return 'discover';
        }
        elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
        {
            return 'jcb';
        }
        elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
        {
            return 'mastercard';
        }
        elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
        {
            return 'visa';
        }
        else
        {
            return 'Unknown';
        }
    }
}
