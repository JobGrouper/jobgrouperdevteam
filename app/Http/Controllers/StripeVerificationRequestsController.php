<?php

namespace App\Http\Controllers;

use App\StripeVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Requests;

class StripeVerificationRequestsController extends Controller
{
    public function edit(Request $request){
        $stripeVerificationRequest = StripeVerificationRequest::findOrFail($request->id);

        return view('pages.account.stripe_verification_form', [
            'fields_needed' => json_decode($stripeVerificationRequest->fields_needed, true),
            'id' => $stripeVerificationRequest->id
        ]);
    }

    public function update(Request $request){
        //update stripe account
        //check $stripeVerificationRequest->completed to true
        $stripeVerificationRequest = StripeVerificationRequest::findOrFail($request->id);
        dd($stripeVerificationRequest);
    }
}
