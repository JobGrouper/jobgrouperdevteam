<?php

namespace App\Console;

use App\EmployeeExitRequest;
use App\Sale;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\User;
use App\ConfirmUsers;
use App\Job;
use PayPal\Api\Amount;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use SebastianBergmann\Environment\Console;
use DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\ChatServer::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        /*
         * Cron task for delete users with overdue email verify
         */
        $schedule->call(function () {
            ConfirmUsers::where('updated_at','<',date('Y-m-d H:i:s', strtotime('-1 hours')))->delete();
            User::where('updated_at','<',date('Y-m-d H:i:s', strtotime('-1 hours')))->where('email_confirmed','=',0)->delete();
        })->everyMinute();

        $schedule->call(function () {
            //This script will check if socket server is running and run it if no
            shell_exec('/home/jobgrou2/public_html/application/storage/shell/run_socket_server.sh');
        })->everyMinute();


        //Delete orders without payments
        $schedule->call(function () {
            $sales = Sale::where('status', 'in_progress')->get();
            foreach ($sales as $sale){
                $payments = $sale->payments();
                if(!$payments->count() && $sale->updated_at < date('Y-m-d H:i:s', strtotime('-5 minutes'))){
                    $job = $sale->job()->first();
                    $sale->delete();
                    if($job->sales_count == 0){
                        $job->work_stop();
                    }
                }
            }
        })->everyMinute();


        //Approving employees exit requests two week old
        $schedule->call(function () {
            $employeeExitRequests = EmployeeExitRequest::where('status', 'pending')->where('created_at','<',date('Y-m-d H:i:s', strtotime('-2 weeks')))->get();
            foreach ($employeeExitRequests as $employeeExitRequest){
                //Апрувим заявку выхода из работы
                $employeeExitRequest->status = 'approved';
                $employeeExitRequest->save();

                $job = $employeeExitRequest->job()->first();

                //Удаляем запрос юзера на выполнение этой работы
                $job->employee_requests()->where('employee_id', $employeeExitRequest->employee_id)->delete();

                //Удаляем исполнитея работы
                $job->employee_id = null;

                //Если есть потенциальный работник - ставим его основным
                if($job->potential_employee_id){
                    $job->employee_id = $job->potential_employee_id;
                    $job->potential_employee_id = null;
                    $job->work_start();
                }
                $job->save();
                $job->work_stop();
            }
        })->everyMinute();


        //Это  для сохранения карт и авто-оплат
        $schedule->call(function () {
            $this->getPaymentsForOrders();
        //})->everyMinute(); //минута час день_месяца месяц день_недели*/
        })->cron('0 0 * * *'); //минута час день_месяца месяц день_недели*/


    }



    private function getPaymentsForOrders(){

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),     // ClientID
                env('PAYPAL_CLIENT_SECRET')      // ClientSecret
            )
        );

        $jobs = Job::where('status', '=', 'working')->where('next_payment_date','<=',date('Y-m-d H:i:s'))->get();
        if($jobs->count() == 0){
            return false;
        }

        Log::info('Start billing operations');

        foreach ($jobs as $job){
            Log::info('Taking job "'.$job->title.'"');

            $job->next_payment_date = Carbon::parse($job->next_payment_date)->addMonth();
            $job->save();

            $employee = $job->employee()->first();
            $orders = $job->sales()->get();

            Log::info('Start getting payments');
            foreach ($orders as $order){
                $creditCard = $order->credit_card()->first();
                if(isset($creditCard->id)){
                    $creditCardToken = new CreditCardToken();
                    $creditCardToken->setCreditCardId($creditCard->card_id);

                    $fi = new FundingInstrument();
                    $fi->setCreditCardToken($creditCardToken);

                    $payer = new Payer();
                    $payer->setPaymentMethod("credit_card")
                        ->setFundingInstruments(array($fi));


                    $amount = new Amount();
                    $amount->setCurrency("USD")
                        ->setTotal($job->monthly_price);

                    $transaction = new Transaction();
                    $transaction->setAmount($amount)
                        ->setDescription("Payment for first month")
                        ->setInvoiceNumber(uniqid());

                    $payment = new Payment();
                    $payment->setIntent("sale")
                        ->setPayer($payer)
                        ->setTransactions(array($transaction));

                    try {
                        $payment->create($apiContext);
                    } catch (\Exception $ex) {
                        Log::info('Error');
                    }

                    $payment = $order->payments()->create([
                        'buyer_id' => $order->buyer_id,
                        'amount' => $job->monthly_price,
                        'month' => $order->month_to_pay,
                        'payment_system' => 'paypal',
                        'status' => 'success',
                    ]);


                }
            }

            Log::info('Finish getting payments');

            /*
            * Send money to seller
            */
            Log::info('Employee paypal: '.$employee->paypal_email);
            if($employee->paypal_email){
                Log::info('Start to pay salary');
                $payouts = new \PayPal\Api\Payout();
                $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
                $senderBatchHeader->setSenderBatchId(uniqid())
                    ->setEmailSubject("You have a Payout!");

                $senderItem = new \PayPal\Api\PayoutItem();
                $senderItem->setRecipientType('Email')
                    ->setNote('Salary for work on job '.$job->title)
                    ->setReceiver($employee->paypal_email)
                    ->setSenderItemId("2014031400023")
                    ->setAmount(new \PayPal\Api\Currency('{
                            "value":"'.$job->monthly_salary.'",
                            "currency":"USD"
                        }'));

                $payouts->setSenderBatchHeader($senderBatchHeader)
                    ->addItem($senderItem);

                $request = clone $payouts;

                try {
                    $output = $payouts->createSynchronous($apiContext);
                } catch (\Exception $ex) {
                    Log::info('Error');
                }
                Log::info('Finish to pay salary');
            }
        }
        Log::info('Finishing billing operations');
    }
}
