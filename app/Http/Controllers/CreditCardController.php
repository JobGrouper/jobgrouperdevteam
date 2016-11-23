<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentServiceInterface;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use URL;

class CreditCardController extends Controller
{
    public function create(){
        Session::flash('continuePurchaseUrl', URL::previous()); //To redirect user to purchase page after card creation
        return view('pages.account.card');
    }
    
    public function store(Request $request, PaymentServiceInterface $psi){
        $user = Auth::user();
        if($user->user_type == 'employee'){
            $cardData = [
                "number" => $request->card_number,
                "exp_month" => $request->end_month,
                "exp_year" => $request->end_year,
                "cvc" => $request->cvv,
            ];

            $token = $psi->createCreditCardToken($cardData, true);

            $psi->createExternalAccount($user, $token);
        }
        else{
            die('Service unavailable for buyers now');
        }


        if(Session::get('continuePurchaseUrl')){
            return redirect(Session::get('continuePurchaseUrl'));
        }
        else{
            Session::flash('message_success', 'Card has been successfully added to your account!');
            return redirect('/card/create');
        }
    }
}
