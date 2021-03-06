<?php

namespace App\Console;

use App\EmployeeExitRequest;
use App\Interfaces\PaymentServiceInterface;
use App\Jobs\CreateRefund;
use App\StripeManagedAccount;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\User;
use App\ConfirmUsers;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Log;
use Stripe\Plan;

use App\Operations\EmployeeExitOP;

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
        Commands\MaintenanceSet::class,
        Commands\MaintenanceClear::class,
	Commands\GenerateStripeVerificationRequest::class,
	Commands\QueueTest::class,
	Commands\PromptForPaymentMethod::class,
	Commands\SendVerificationRequest::class,
	Commands\VerifyEmployees::class,
	Commands\StopJob::class,
	Commands\LogTest::class,
	Commands\TestStripePlanActivation::class
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
	    /*
        $schedule->call(function () {
            ConfirmUsers::where('updated_at','<',date('Y-m-d H:i:s', strtotime('-1 hours')))->delete();
            User::where('updated_at','<',date('Y-m-d H:i:s', strtotime('-1 hours')))->where('email_confirmed','=',0)->delete();
        })->everyMinute();
	     */

        /*
         * Task to run chat socket server and check it`s status
         */
        /*
            $schedule->call(function () {
                //This script will check if socket server is running and run it if no
                shell_exec('storage/shell/run_socket_server.sh');
            })->everyMinute();
         */


        //Approving employees exit requests two week old
        $schedule->call(function () {
            //$this->dispatch(new CreateRefund());
	    //Getting employee`s exit requests that are older than 2 weeks
	    $employeeExitRequests = EmployeeExitRequest::where('status', 'pending')->where('created_at','<',date('Y-m-d H:i:s', strtotime('-2 weeks')))->get();

	    foreach ($employeeExitRequests as $employeeExitRequest){
		$op = \App::make('App\Operations\EmployeeExitOP');
		$op->go($employeeExitRequest);
	    }

        })->everyMinute();
    }
}
