<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\SocialAccountService;
use Socialite;
use Auth;

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
        return redirect('/account');
    }
}
