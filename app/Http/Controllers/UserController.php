<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentServiceInterface;
use Illuminate\Http\Request;
use DB;
use Validator;

use App\Http\Requests;
use Auth;
use App\User;

class UserController extends Controller
{
    public function myAccount(){
        $user = Auth::user();

	$card = NULL;

	if ($user->user_type == 'employee') {

		// This query is probably wrong
		// 	needs to be sorted by date
		$card = DB::table('stripe_managed_accounts')->
			join('stripe_external_accounts', 'stripe_managed_accounts.id', '=', 'stripe_external_accounts.managed_account_id')->
			where('stripe_managed_accounts.user_id', $user->id)->latest('stripe_external_accounts.created_at')->first();
	}

        return view('pages.account', ['user' => $user, 'card' => $card]);
    }

    public function showAccount($userID){
        $user = User::findOrFail($userID);
        /*if(Auth::user()->id == $userID){
            return redirect('/account');
        }*/
        return view('pages.employee_profile', ['user' => $user]);
    }

    public function update(Request $request){
        $response = array();

        $input = $request->only(['first_name', 'last_name', 'linkid_url', 'fb_url', 'git_url', 'paypal_email']);
        $user = Auth::user();
        //$user = User::where('email', '=', 'ovch2008@ukr.net')->first();
        $user->fill($request->all());
        $user->save();

        if(isset($request->image_hash)){
            $imageHash = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image_hash));
            $imageName = 'users/u_'.$user->id.'.png';
            if(!file_put_contents(config('app.images_dir').$imageName, $imageHash)){
                $responseData['error'] = true;
                $responseData['status'] = -1;
                $responseData['info'] = 'Something went wrong. Please try again later.';
                return response($responseData, 500);
            }
        }

        $response['errors'] = false;
        $response['status'] = 0;
        $response['info'] = 'User data successfully updated!';

        return response($response, 200);
    }


    public function showJobs(){
        $employee = Auth::user();
        if($employee->user_type != 'employee'){
            return redirect('/');
        }

        $jobs = $employee->jobs()->where('status', 'working')->get();
	$jobsAwaitingActivation = $employee->jobs()->where('status', 'waiting')->get();

        $employeeRequests = $employee->employee_requests()->where('status', 'pending')->get();
        $potentialJobs = $employee->potential_jobs()->get();

        return view('pages.account.my_jobs', compact('jobs', 'jobsAwaitingActivation',  'potentialJobs', 'employeeRequests'));
    }

    public function showOrders(){
        $buyer = Auth::user();
        if($buyer->user_type != 'buyer'){
            return redirect('/');
        }

        $orders = $buyer->orders()->where('status', 'in_progress')->orWhere('status', '=', 'pending')->get();
  

        return view('pages.account.my_orders', ['orders' => $orders, 'early_bird' => NULL]);
    }

    public function showPayments(){
        $buyer = Auth::user();
        if($buyer->user_type != 'buyer'){
            return redirect('/');
        }

        $payments = $buyer->payments()->get();


        return view('pages.account.payments_history', compact('payments'));
    }

    public function deactivate($user_id){
        $user = User::findOrFail($user_id);
        if($user->role != 'admin'){
            if($user->active){
                $user->update(['active' => false]);
                die('deactivated');
            }
            else{
                $user->update(['active' => true]);
                die('activated');
            }
        }
    }

    /*public function createStripeCustomerSource(Request $request, PaymentServiceInterface $psi){
        $response = array();

        $user = Auth::user();
        $cardToken = $psi->createCreditCardToken([
            "number" => $request->number,
            "exp_month" => $request->exp_month,
            "exp_year" => $request->exp_year,
            "cvc" => $request->cvc,
            "currency" => $request->currency
        ], true);

        $psi->updateCustomerSource($user, $cardToken);

        $response['errors'] = false;
        $response['status'] = 0;
        $response['info'] = 'Stripe customer source created successfully!';
        return response($response, 200);
    }*/

    public function createStripeExternalAccount(Request $request, PaymentServiceInterface $psi){
        $response = array();
        $user = Auth::user();
        $user = User::where('id', 5)->first();

        if($user->user_type == 'employee'){
            $cardToken = $psi->createCreditCardToken([
                "number" => $request->number,
                "exp_month" => $request->exp_month,
                "exp_year" => $request->exp_year,
                "cvc" => $request->cvc,
                "currency" => 'usd'
            ], 'card', true);

            $psi->createExternalAccount($user, $cardToken);

            $response['errors'] = false;
            $response['status'] = 0;
            $response['info'] = 'Stripe external account created successfully!';
            return response($response, 200);
        }
    }

    public function showChangePassword() {
            return view('auth.passwords.change_password_form');
    }

    public function changePassword(Request $request) {

	    $user = Auth::user();

	    $validator = Validator::make($request->all(), [
		    'current_password' => 'required',
		    'new_password' => 'required|min:6|max:255|different:current_password',
		    'confirm_new_password' => 'required|same:new_password'
	    ], [
	    	    'same' => 'The new password fields must match',
	    	    'different' => 'The new password must be different than the current password'		    
	    ]);


	    $validator->after(function($validator) use ($request, $user) {

		    // Make sure current password matches
		    if (Auth::attempt(['email' => $user->email, 'password' => $request->input('current_password')])) {
			    $user->password = bcrypt($request->new_password);
			    $user->save();
		    } else {

			    // If not...
			    $validator->errors()->add('current_password', 'The password you entered is incorrect');
		    }
	    });

            if ($validator->fails()) {
		return redirect()->back()
			->withErrors($validator);
            }
	    else {
		return view('auth.passwords.change_password_success');
	    }
    }
}
