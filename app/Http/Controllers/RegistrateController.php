<?php

namespace App\Http\Controllers;

use App\StripeVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Interfaces\PaymentServiceInterface;
use App\User;
use App\UserSocialAccount;
use App\ConfirmUsers;
use Mail;
use Auth;
use DB;
use Illuminate\Support\Facades\Session;


class RegistrateController extends Controller
{

    public function checkEmailFree(Request $request){
        $response = array();
        $user = User::where('email', '=', $request->input('email'))->first();
        if ($user === null) {
            $response['status'] = 0;
            $response['info'] = 'Email free';
        }
        else{
            $response['status'] = 1;
            $response['info'] = 'User with this email already exist';
        }

        return response($response, 200);
    }


    public function register(Request $request, PaymentServiceInterface $psi)
    {
        switch($request->user_type){
            case 'buyer':
                $this->validate($request, [
                    'first_name' => 'required|min:2|max:255',
                    'last_name' => 'required|min:2|max:255',
                    'email' => 'required|unique:users|max:255',
                    'password' => 'required|min:6|max:255',
                ]);
                break;
            case 'employee':
                $this->validate($request, [
                    'first_name' => 'required|min:2|max:255',
                    'last_name' => 'required|min:2|max:255',
                    'email' => 'required|unique:users|max:255',
                    'password' => 'required|min:6|max:255',
                    'city' => 'required',
                    'address' => 'required',
                    'postal_code' => 'required',
                    'state' => 'required',
                    'dob_day' => 'required',
                    'dob_month' => 'required',
                    'dob_year' => 'required',
                    'ssn_last_4' => 'required',
                ]);
                break;
            default:
                die('User type error');
        }

        //Creating new user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'user_type' => $request->input('user_type'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);


        if(isset($user->id))
        {
            //Creating record for user email confirmation
            $email = $user->email;
            $token = str_random(32);
            $confirmUser = new ConfirmUsers;
            $confirmUser->email = $email;
            $confirmUser->token = $token;
            $confirmUser->save();


            //Check if registration began from creating the social account (fb / tw)
            if($request->input('social_account_id')){
                $socialAccount = UserSocialAccount::whereId($request->input('social_account_id'))
                    ->whereUserId(null)->get()->first();

                //Associating user account with user social account
                $socialAccount->user()->associate($user);
                $socialAccount->save();
            }

            if($user->user_type == 'employee'){
                //Creating Stripe Managed Account
                $stripeAccountData = [
                    "country" => "US",
                    "email" => $request->email,
                    "legal_entity" => [
                        "address" => [
                            "city" => $request->city,
                            "line1" => $request->address,
                            "postal_code" => $request->postal_code,
                            "state" => $request->state
                        ],
                        "dob" => [
                            "day" => $request->dob_day,
                            "month" => $request->dob_month,
                            "year" => $request->dob_year
                        ],
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "ssn_last_4" => $request->ssn_last_4,
                        "type" => 'individual',
                    ],
                    "tos_acceptance" => [
                        "date" => time(),
                        "ip" => $request->ip()
                    ]
                ];

               $response = $psi->createAccount($stripeAccountData, $user->id);

		if (isset($response['error'])) {

			// delete everything
			$user->delete();
			$confirmUser->delete();

			$error = 'Sorry, there was an error on our end. Try back later.';

			if ($response['user'] == True) {
				$error = $response['message'];
			}

			return redirect('/register')->
				withErrors([ $error ]);
		}
            }

	    //Sending confirmation mail to user
	    Mail::send('emails.confirm',['token'=>$token],function($u) use ($user)
	    {
		$u->from('admin@jobgrouper.com');
		$u->to($user->email);
		$u->subject('Confirm Registration');
	    });
        }
        else {
            die('Something went wrong. Please try again later.');
        }
        return view('pages.success');
    }

    public function confirm($token)
    {
        $model = ConfirmUsers::where('token','=',$token)->first();
        if(!count($model)){
            die('Token not found!');
        }
        else{
            $user = User::where('email','=',$model->email)->first();
            $user->email_confirmed = 1;
            $user->save();
            $model->delete();

            Auth::login($user);

            if(Session::get('last_visited_job')){
                $jobID = Session::pull('last_visited_job');
                return redirect('/job/'.$jobID);
            }
            
            if($user->user_type == 'employee'){

		    //Sending confirmation mail to user
		    Mail::send('emails.seller_confirm', [] ,function($u) use ($user)
		    {
			$u->from('admin@jobgrouper.com');
			$u->to($user->email);
			$u->subject('Final Steps for Verification');
		    });

		return redirect('/account');
            }
            else{
                return redirect('/');
            }
        }
    }

    public function getMoreVerification($id) {
        if (Auth::check()) {
            $user = Auth::user();
            $stripeVerificationRequest = StripeVerificationRequest::find($id);
            if($user->can('edit', $stripeVerificationRequest) && $stripeVerificationRequest->completed == false){
                return view('pages.additional_verification', [
                    'fields_needed' => json_decode($stripeVerificationRequest->fields_needed, true),
                    'id' => $stripeVerificationRequest->id
                ]);
            }
            else{
                dd('This action is unauthorized.');
            }
        }
        else{
            Session::put('stripe_verification_request', $id);
            return redirect('/login');
        }
    }
}
