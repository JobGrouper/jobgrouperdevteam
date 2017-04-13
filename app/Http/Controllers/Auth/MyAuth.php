<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MyAuth extends Controller
{
    public function auth(Request $request) {
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'email_confirmed' => '1']))
        {
            $user = Auth::user();
            if($user->active){
                if(Session::get('last_visited_job')){
                    $jobID = Session::pull('last_visited_job');
                    return redirect('/job/'.$jobID);
                }

                if($user->user_type == 'employee'){
                    if(Session::get('stripe_verification_request')){
                        $stripeVerificationRequestsId = Session::pull('stripe_verification_request');
                        return redirect('/account/additional_info/'.$stripeVerificationRequestsId);
                    }
                    return redirect('/my_jobs');
                }
                else{
                    return redirect('/my_orders');
                }
            }
            else{
                Auth::logout();
                return back()->with('message','Your account banned! Contact the support!');
            }
        }
        else {
            return back()->with('message','Invalid credentials');
        }

    }
}