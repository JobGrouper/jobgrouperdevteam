<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentServiceInterface;
use App\StripeVerificationRequest;
use Illuminate\Http\Request;
use Session;

use App\Http\Requests;

class StripeVerificationRequestsController extends Controller
{
    public function update(Request $request, PaymentServiceInterface $psi){

        $stripeVerificationRequest = StripeVerificationRequest::findOrFail($request->id);

        $this->authorize('edit', $stripeVerificationRequest);

        $stripeAccountData = $request->stripeAccountData;
        $stripeAccountData['tos_acceptance']['date'] = time();
        //$stripeAccountData['tos_acceptance']['ip'] = $_SERVER['REMOTE_ADDR'];
        $stripeAccountData['tos_acceptance']['ip'] = '89.252.17.119';   //for testing on local machine

        //If file of verification document was attached.
        if ($request->hasFile('verification_document')) {
            $fileName = md5(uniqid()).'.'.$request->verification_document->getClientOriginalExtension();
            $request->file('verification_document')->move('temp/', $fileName);
            $document_id = $psi->uploadDocument('temp/'.$fileName, $stripeVerificationRequest->managed_account_id);
            unlink('temp/'.$fileName);  //remove temporary file
            unset($request->verification_document);
            $stripeAccountData['legal_entity']['verification']['document'] = $document_id;
        }

        $response = $psi->updateAccount($stripeVerificationRequest->managed_account_id, $stripeAccountData);

        if($response){
            $stripeVerificationRequest->completed = true;
            $stripeVerificationRequest->save();
            dd('Success'); //Redirect to "success page" will be here
        }
        else{
            Session::flash('message_error', 'Unknown error.');
            return redirect()->back();
        }
    }
}
