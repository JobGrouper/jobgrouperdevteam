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

        $this->validate($request, [
            'stripeAccountData.legal_entity.address.city' => 'min:2|max:255',
            'stripeAccountData.legal_entity.address.line1' => 'min:3|max:255',
            'stripeAccountData.legal_entity.address.postal_code' => 'digits:5',
            'stripeAccountData.legal_entity.address.state' => 'size:2',

            //'stripeAccountData.legal_entity.business_name' => 'min:2|max:255',

            //'stripeAccountData.legal_entity.business_tax_id' => 'min:2|max:255',

            'stripeAccountData.legal_entity.dob.day' => 'digits_between:1,31',
            'stripeAccountData.legal_entity.dob.month' => 'digits_between:1,12',
            'stripeAccountData.legal_entity.dob.year' => 'digits_between:1,'.date("Y"),

            'stripeAccountData.legal_entity.first_name' => 'min:2|max:255',
            'stripeAccountData.legal_entity.last_name' => 'min:2|max:255',

            'stripeAccountData.legal_entity.ssn_last_4' => 'digits:4',

            'stripeAccountData.legal_entity.type' => 'in:individual,company',

            'stripeAccountData.legal_entity.personal_id_number' => 'min:2|max:255',
        ]);

        $stripeAccountData = $request->stripeAccountData;
        $stripeAccountData['tos_acceptance']['date'] = time();
        //$stripeAccountData['tos_acceptance']['ip'] = $_SERVER['REMOTE_ADDR'];
        //$stripeAccountData['tos_acceptance']['ip'] = '89.252.17.119';   //for testing on local machine
	$stripeAccountData['tos_acceptance']['ip'] = $request->ip();

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
	    return view('pages.success_additional_verification');
        }
        else{
            Session::flash('message_error', 'Unknown error.');
            return redirect()->back();
        }
    }
}
