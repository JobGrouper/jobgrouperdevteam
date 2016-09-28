<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id' => '179542855756102',
        'client_secret' => 'a454d22cfbd3426ea4699d9797468c67',
        'redirect' => 'http://jobgrouper.com/social_login/callback/facebook',
    ],

    'twitter' => [
        'client_id' => 'siVjlagVI0MCuZ1lVRBE5ySUW',
        'client_secret' => 'W4dnzAvvMgg0RWNNcaTVTIlhVk2jbsMSSQT8yvBwo8Rixk7Kj2',
        'redirect' => 'http://jobgrouper.com/social_login/callback/twitter',
    ],

];
