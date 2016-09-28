<?php
/**
 * Created by PhpStorm.
 * User: Админ
 * Date: 29.06.2016
 * Time: 12:52
 */

namespace App\Services;

use App\UserSocialAccount;
use App\User;

class SocialAccountService
{
    public function createOrGetUser($providerObj, $providerName)
    {

        //Check if UserSocialAccount already exist
        $providerUser = $providerObj->user();
        $socialAccount = UserSocialAccount::whereProvider($providerName)
            ->whereProviderUserId($providerUser->getId())
            ->first();


        if ($socialAccount) {               //If UserSocialAccount already exist
            $user = $socialAccount->user;
            if($user !== null){     //If user for this social account created
                $user['registered'] = true;; //Flag t show that it is registered user
                return $user;
            }

            $fullName = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $providerUser->getName());
            list($first_name, $last_name) = explode(' ', $fullName);
            return ([
                'social_account_id' => $socialAccount->id,
                'email' => $providerUser->getEmail(),
                'first_name' => $first_name,
                'last_name' => $last_name,
            ]);
        }
        else {                              //If UserSocialAccount not exist
            $socialAccount = new UserSocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $providerName]);

            $socialAccount->save();

            $fullName = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $providerUser->getName());
            list($first_name, $last_name) = explode(' ', $fullName);
            return ([
                'social_account_id' => $socialAccount->id,
                'email' => $providerUser->getEmail(),
                'first_name' => $first_name,
                'last_name' => $last_name,
            ]);

            //$user = User::whereEmail($providerUser->getEmail())->first();
            //$user = User::createBySocialProvider($providerUser);
            //$socialAccount->user()->associate($user);
        }
    }
}