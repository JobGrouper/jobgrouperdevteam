<?php

namespace App\Http\Controllers;

use App\PaymentServices\StripeService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Interfaces\PaymentServiceInterface;

use App\User;
use App\UserSocialAccount;
use App\ConfirmUsers;
use Mail;
use Auth;
use Carbon\Carbon;
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


    public function register(Request $request, StripeService $ss)
    {
        $user = User::where('email', '=', $request->input('email'))->first();
        if ($user !== null) {
            die('User with this email already exist');
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
            $token = str_random(32);            //token for email confirmation
            $confirmUser = new ConfirmUsers;
            $confirmUser->email = $email;
            $confirmUser->token = $token;
            $confirmUser->save();


            //Check if registration began from creating the social account (fb / tw)
            if($request->input('social_account_id')){
                $socialAccount = UserSocialAccount::whereId($request->input('social_account_id'))
                    ->whereUserId(null)
                    ->first();

                //Associating user account with user social account
                $socialAccount->user()->associate($user);
                $socialAccount->save();
            }

            if($user->type == 'employee'){
                //Creating Stripe Managed Account
                $stripeAccountData = [
                    "country" => $request->country,
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
                $ss->createAccount($stripeAccountData, $user->id);
            }
            else{
                //Creating Stripe Costumer Account
            }



            //Sending confirmation mail to user
            Mail::send('emails.confirm',['token'=>$token],function($u) use ($user)
            {
                $u->from('admin@jobgrouper.com');
                $u->to($user->email);
                $u->subject('Confirm registration');
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
            
            if($user->user_type == 'employee'){
                return redirect('/account');
            }
            else{
                //If buyer come to login from card buy process - redirect him to that card
                if(Session::get('formJob')){
                    $jobID = Session::get('formJob');
                    Session::forget('formJob');
                    return redirect('/job/'.$jobID);
                }

                return redirect('/');
            }
        }
    }
}
