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
                if($user->user_type == 'employee'){
                    return redirect('/my_jobs');
                }
                else{
                    //If buyer come to login from card buy process - redirect him to that card
                    if(Session::get('formJob')){
                        $jobID = Session::get('formJob');
                        Session::forget('formJob');
                        return redirect('/job/'.$jobID);
                    }

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