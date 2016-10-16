<?php
use Illuminate\Contracts\Bus\Dispatcher;
use App\Console\Commands\ChatServer;
use App\Job;


use PayPal\Api\Amount;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\CreditCardToken;
use Carbon\Carbon;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', 'PagesController@home');


//Route::get('register', 'RegistrateController@showRegisterPage');
Route::post('custom_register', 'RegistrateController@register');
Route::post('auth/login', 'Auth\MyAuth@auth');
Route::get('register/confirm/{token}', 'RegistrateController@confirm');
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

Route::get('job/{id}','JobController@show');
Route::get('jobs','JobController@all');
Route::get('jobs/category/{id?}','JobController@all');
Route::post('job/store','JobController@store');
Route::post('job/update','JobController@update');


Route::get('account', ['middleware' => 'auth', 'uses' => 'UserController@myAccount']);
Route::get('account/{userID}','UserController@showAccount');

Route::get('my_jobs', ['middleware' => 'auth', 'uses' => 'UserController@showJobs']);
Route::get('my_orders', ['middleware' => 'auth', 'uses' => 'UserController@showOrders']);
Route::get('my_transactions', ['middleware' => 'auth', 'uses' => 'UserController@showPayments']);


Route::get('help','PagesController@help');
Route::get('terms','PagesController@terms');

Route::get('messages/{recipientID?}', ['as' => 'messages', 'middleware' => 'auth', 'uses' => 'MessageController@index']);

Route::get('social_login/{provider}', 'SocialAuthController@login');
Route::get('social_login/callback/{provider}', 'SocialAuthController@callback');



Route::get('card/create', ['middleware' => 'auth', 'uses' => 'CreditCardController@create']);
Route::post('card/store', 'CreditCardController@store');

Route::post('category/store', 'CategoryController@store');

//Route::get('purchase/{jobID}', ['middleware' => 'auth', 'uses' => 'OrderController@create']);

/**
 * Operations with Orders (Sales)
 */
Route::get('purchase/{order_id}', ['middleware' => 'auth', 'uses' => 'OrderController@purchase']);
Route::get('change_credit_card/{order_id}', ['middleware' => 'auth', 'uses' => 'OrderController@change_credit_card']);
Route::put('change_credit_card', ['middleware' => 'auth', 'uses' => 'OrderController@set_new_credit_card']);
Route::post('order/store', 'OrderController@store');
Route::put('order', 'OrderController@update');
Route::post('order/purchase_via_stripe', 'OrderController@purchaseViaStripe');
Route::post('order/close/{order_id}', 'OrderController@close');




Route::post('/employee_request/approve', ['middleware' => 'check_role', 'uses' => 'EmployeeRequestController@approve']);
Route::post('/employee_request/reject', ['middleware' => 'check_role', 'uses' => 'EmployeeRequestController@reject']);



Route::group(['prefix' => 'admin', 'middleware' => 'check_role'], function () {
    Route::get('/cards', ['as' => 'cards', 'uses' => 'PagesAdminController@cards']);
    Route::get('/', ['as' => 'users', 'uses' => 'PagesAdminController@users']);
    Route::get('/users', ['as' => 'users', 'uses' => 'PagesAdminController@users']);
    Route::get('/card', ['as' => 'cards', 'uses' => 'JobController@create']);
    Route::get('/card/{job_id}/edit', ['uses' => 'JobController@edit']);
    Route::get('/categories', ['as' => 'categories', 'uses' => 'PagesAdminController@categories']);
    Route::get('/employee_requests/{job_id}', ['as' => 'employee_requests', 'uses' => 'PagesAdminController@employee_requests']);
    Route::get('/orders/{job_id}', ['uses' => 'PagesAdminController@orders']);
    Route::get('/texts', ['as' => 'texts', 'uses' => 'PagesAdminController@texts']);
});



Route::group(['prefix' => 'api'], function () {
    Route::post('order/close/{order_id}', 'OrderController@close');

    Route::post('checkEmailFree', 'RegistrateController@checkEmailFree');
    Route::put('user/update', 'UserController@update');

    Route::get('dialogs','MessageController@getDialogs');
    Route::get('messages_history/{recipientID}','MessageController@getMessagesHistory');
    Route::post('markMessageasAsRead/{recipientID}','MessageController@markMessageasAsRead');
    Route::get('countNewMessages/{recipientID?}','MessageController@countNewMessages');

    Route::post('experience', 'ExperienceController@store');
    Route::put('experience/{id}', 'ExperienceController@update');
    Route::delete('experience/{id}', 'ExperienceController@destroy');

    Route::post('education', 'EducationController@store');
    Route::put('education/{id}', 'EducationController@update');
    Route::delete('education/{id}', 'EducationController@destroy');

    Route::post('addition', 'AdditionController@store');
    Route::put('addition/{id}', 'AdditionController@update');
    Route::delete('addition/{id}', 'AdditionController@destroy');

    Route::post('skill', 'SkillController@store');
    Route::delete('skill/{id}', 'SkillController@destroy');

    Route::post('employeeRequest/{job_id}', 'EmployeeRequestController@store');
    Route::delete('/employeeRequest/{employee_request_id}', ['uses' => 'EmployeeRequestController@destroy']);
    Route::post('employeeExitRequest/{job_id}', 'EmployeeExitRequestController@store');
    Route::post('closeOrderRequest/{order_id}', 'CloseOrderRequestController@store');

    Route::post('deactivateUser/{user_id}', ['middleware' => 'check_role', 'uses' => 'UserController@deactivate']);
    Route::post('deleteJob/{job_id}', ['middleware' => 'check_role', 'uses' => 'JobController@destroy']);
    Route::put('category/{id}', ['middleware' => 'check_role', 'uses' => 'CategoryController@update']);
    Route::delete('category/{category_id}/{new_category_id}', ['middleware' => 'check_role', 'uses' => 'CategoryController@destroy']);

    Route::put('texts/{id}', ['middleware' => 'check_role', 'uses' => 'PageTextsController@update']);

    Route::post('order/purchase_via_stripe', 'OrderController@purchaseViaStripe');

    Route::post('rate/{rated_id}', 'RateController@store');

    //Route::post('purchase/paypal/feedback', ['uses' => 'PayPalController@processPayment']);
    Route::post('purchase/paypal/feedback', ['uses' => 'OrderController@purchasePayPalFeedback']);

});

Route::group(['prefix' => 'test'], function(){
    Route::get('paypal', function () {
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),     // ClientID
                env('PAYPAL_CLIENT_SECRET')      // ClientSecret
            )
        );

	$apiContext->setConfig(array('mode' => env('PAYPAL_API_MODE')));

        $payouts = new \PayPal\Api\Payout();
        $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a Payout!");

        $senderItem = new \PayPal\Api\PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setNote('Salary for work on job.')
            ->setReceiver('ken-buyer-1@jobgrouper.com')
            ->setSenderItemId("2014031400023")
            ->setAmount(new \PayPal\Api\Currency('{
                        "value":"10",
                        "currency":"USD"
                    }'));

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        $request = clone $payouts;

        try {
            $output = $payouts->createSynchronous($apiContext);
        } catch (\Exception $ex) {
            dd($ex);
        }

        dd('success');

    });
});


Route::auth();
