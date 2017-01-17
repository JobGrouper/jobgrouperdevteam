<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;

class PromptForPaymentMethod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'op:prompt_for_payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Load file
	$json_file = json_encode(file_get_contents(''), True);

	$users = array_merge($json_file['verified_no_account'], $json_file['unverified_no_account']);
	
	// Prompt
	foreach($users as $user) {
		/*
		$id = $user['account_id'];
		$user = DB::table('users')->
			join('stripe_managed_accounts', 'users.id', '=', 'stripe_managed_accounts.user_id')->
			where('stripe_managed_accounts.id', '=', $id)->first();
		*/

		Mail::send('emails.prompt_for_payment_method', [], function($u) use ($user) {
			$u->from('admin@jobgrouper.com');
			$u->to($user['email']);
			$u->subject('We need you to add a payment method');
		}
	}
    }
}
