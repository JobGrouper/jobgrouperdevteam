<?php
use Illuminate\Contracts\Bus\Dispatcher;
use App\Console\Commands\ChatServer;
use App\Job;
use App\Interfaces\PaymentServiceInterface;


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
Route::get('/zh', 'PagesController@zhTest');

/*
 * Registration, Auth and Password Recovery
 */
Route::post('custom_register', 'RegistrateController@register');
Route::post('auth/login', 'Auth\MyAuth@auth');
Route::get('register/confirm/{token}', 'RegistrateController@confirm');
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');
Route::get('password/change', 'UserController@showChangePassword');
Route::post('password/change_submit', 'UserController@changePassword');
Route::get('social_login/{provider}', 'SocialAuthController@login');
Route::get('social_login/callback/{provider}', 'SocialAuthController@callback');
Route::get('account/additional_info/{id}', 'RegistrateController@getMoreVerification');
Route::put('stripe_verification_request/{id}', 'StripeVerificationRequestsController@update');
Route::auth();


/*
 * Job Cards
 */
Route::pattern('job', '(?i)job(?-i)');
Route::get('{job}/{id}', ['middleware' => 'lowercase', 'uses' => 'JobController@show']);
Route::get('jobs','JobController@all');
Route::get('jobs/category/{id?}','JobController@all');
Route::post('job/store','JobController@store');
Route::post('job/update','JobController@update');


/*
 * Users profile
 */
Route::get('account', ['middleware' => 'auth', 'uses' => 'UserController@myAccount']);
Route::get('account/{userID}','UserController@showAccount');
Route::get('my_jobs', ['middleware' => 'auth', 'uses' => 'UserController@showJobs']);
Route::get('my_orders', ['middleware' => 'auth', 'uses' => 'UserController@showOrders']);
Route::get('my_transactions', ['middleware' => 'auth', 'uses' => 'UserController@showPayments']);
Route::get('card/create', ['middleware' => 'auth', 'uses' => 'CreditCardController@create']);   //User`s credit card
Route::post('card/store', 'CreditCardController@store');
Route::post('card/employee/create', ['middleware' => 'auth', 'uses' => 'CreditCardController@storeEmployeePaymentMethod']);


/*
 * Static pages
 */
Route::get('help','PagesController@help');
Route::get('terms','PagesController@terms');


/*
 * Message system
 */
Route::get('messages/{recipientID?}', ['as' => 'messages', 'middleware' => 'auth', 'uses' => 'MessageController@index']);


/*
 * Operations with Orders (Sales)
 */
Route::get('purchase/{order_id}', ['middleware' => 'auth', 'uses' => 'OrderController@purchase']);
Route::get('change_credit_card/{order_id}', ['middleware' => 'auth', 'uses' => 'OrderController@change_credit_card']);
Route::put('change_credit_card', ['middleware' => 'auth', 'uses' => 'OrderController@set_new_credit_card']);
Route::post('order/store', 'OrderController@store');
Route::put('order', 'OrderController@update');
Route::post('order/purchase_via_stripe', 'OrderController@purchaseViaStripe');


/*
 * Admin`s side routes
 */
Route::group(['prefix' => 'admin', 'middleware' => 'check_role'], function () {
    Route::get('/cards', ['as' => 'cards', 'uses' => 'PagesAdminController@cards']);
    Route::get('/', ['as' => 'users', 'uses' => 'PagesAdminController@users']);
    Route::get('/users', ['as' => 'users', 'uses' => 'PagesAdminController@users']);
    Route::get('/card', ['as' => 'cards', 'uses' => 'JobController@create']);
    Route::get('/card/{job_id}/edit', ['uses' => 'JobController@edit']);
    Route::get('/buyer_adjustment/{job_id}', ['uses' => 'PagesAdminController@create_buyer_request']);
    Route::get('/buyer_adjustment/{job_id}/{request_id}', ['uses' => 'PagesAdminController@review_buyer_request']);
    Route::get('/categories', ['as' => 'categories', 'uses' => 'PagesAdminController@categories']);
    Route::get('/employee_requests/{job_id}', ['as' => 'employee_requests', 'uses' => 'PagesAdminController@employee_requests']);
    Route::get('/orders/{job_id}', ['uses' => 'PagesAdminController@orders']);
    Route::get('/texts', ['as' => 'texts', 'uses' => 'PagesAdminController@texts']);
    Route::get('/emails', ['as' => 'emails', 'uses' => 'PagesAdminController@emails']);
    Route::get('/renderEmailTemplate/{name}', ['uses' => 'EmailsTemplatesController@renderEmail']);
});

Route::post('category/store', 'CategoryController@store');
Route::post('/employee_request/approve', ['middleware' => 'check_role', 'uses' => 'EmployeeRequestController@approve']);
Route::post('/employee_request/reject', ['middleware' => 'check_role', 'uses' => 'EmployeeRequestController@reject']);

Route::post('/buyer_adjustment_requests', ['uses' => 'BuyerAdjustmentController@create_request']);



/*
 * API routes
 */
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
    Route::get('employeeRequestStatus/{id}', 'EmployeeRequestController@getStatus');

    Route::post('deactivateUser/{user_id}', ['middleware' => 'check_role', 'uses' => 'UserController@deactivate']);
    Route::post('deleteJob/{job_id}', ['middleware' => 'check_role', 'uses' => 'JobController@destroy']);
    Route::put('category/{id}', ['middleware' => 'check_role', 'uses' => 'CategoryController@update']);
    Route::delete('category/{category_id}/{new_category_id}', ['middleware' => 'check_role', 'uses' => 'CategoryController@destroy']);

    Route::put('texts/{id}', ['middleware' => 'check_role', 'uses' => 'PageTextsController@update']);

    Route::post('order/purchase_via_stripe', 'OrderController@purchaseViaStripe');

    Route::post('rate/{rated_id}', 'RateController@store');

    //Route::post('stripe_customer_sources', 'UserController@createStripeCustomerSource');
    Route::post('stripe_external_account', 'UserController@createStripeExternalAccount');

    Route::post('stripe/invoice/payment', 'StripeWebhookController@onInvoicePaid');
    Route::post('stripe/invoice/created', 'StripeWebhookController@onInvoiceCreated');
    Route::post('stripe/invoice/failed', 'StripeWebhookController@onInvoiceFailure');
    Route::post('stripe/account/updated', 'StripeWebhookController@onAccountUpdated');
    Route::post('stripe/payout/created', 'StripeWebhookController@onPayoutCreated');
    Route::post('stripe/payout/failed', 'StripeWebhookController@onPayoutFailed');
    Route::post('stripe/payout/updated', 'StripeWebhookController@onPayoutUpdated');
    Route::post('stripe/payout/paid', 'StripeWebhookController@onPayoutPaid');

    Route::post('/buyerAdjustment', ['middleware' => 'check_role', 'uses' => 'BuyerAdjustmentController@create']);
    Route::post('/buyerAdjustmentRequest', ['uses' => 'BuyerAdjustmentController@create_request']);
    Route::post('/denyBuyerAdjustmentRequest/{request_id}', ['middleware' => 'check_role', 'uses' => 'BuyerAdjustmentController@deny_request']);
    Route::post('/requestStartWorkNow', ['uses' => 'BuyerAdjustmentController@requestStartWorkNow']);
    Route::post('/startWorkNow', 'BuyerAdjustmentController@startWorkNow');
});




Route::get('translate/{lang}', 'LocalizationController@SetLocalization');

//Sandbox, for testing some features
Route::get('sandbox', 'TestController@test');

