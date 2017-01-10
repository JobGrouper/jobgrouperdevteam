<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\SocialAccountService;
use Socialite;
use Auth;
use Illuminate\Support\Facades\Session;

class SocialAuthController extends Controller
{
    public function login($provider)
    {
        return Socialite::with($provider)->redirect();
    }

    public function callback(SocialAccountService $service, $provider)
    {
        $driver   = Socialite::driver($provider);
        $user = $service->createOrGetUser($driver, $provider);
        if(isset($user['social_account_id'])){
            return view('auth.social-register-finish', ['userData' => $user]);
        }

        Auth::login($user);
        if(Session::get('last_visited_job')){
            $jobID = Session::pull('last_visited_job');
            return redirect('/job/'.$jobID);
        }
        return redirect('/account');
    }
}
