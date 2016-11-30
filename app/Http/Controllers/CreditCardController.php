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

    public function storeSellerAccount(Request $request, PaymentServiceInterface $psi) {

	$user = Auth::user();

	$accountData = NULL;

	if (!isset($request->account_type)) {
		die('No account type set');
	}

	if ($request->account_type == 'bank_account') {

		$accountData = array(
			"account_holder_name" => $request->account_name,
			"account_holder_type" => "individual",
			"routing_number" => $request->routing_number,
			"account_number" => $request->account_number
		);
	}
	else if ($request->account_type == 'debit') {

		$accountData = array(
			"number" => $request->card_number,
			"exp_month" => $request->end_month,
			"exp_year" => $request->end_year,
			"cvc" => $request->cvv,
		);
	}


        $token = $psi->createCreditCardToken($accountData, 'card', true);

	$response = $psi->createExternalAccount($user, $token);

	// Back to account page
        return redirect('/account');
    }
}
