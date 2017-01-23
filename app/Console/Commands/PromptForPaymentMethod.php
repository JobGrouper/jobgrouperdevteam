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
    protected $signature = 'op:prompt_for_payment_method';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Onetime command to ask customers to add a payment method';

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
	$json_file = json_decode(file_get_contents('storage/files/preexisting-accounts.json'), True);

	if (!$json_file) {
		throw new \Exception('PromptForPaymentMethod:: the account file couldn\'t be opened');
	}

	$users = array_merge($json_file['verified_without_account'], $json_file['unverified_without_account']);
	
	// Prompt
	foreach($users as $user) {

		Mail::send('emails.prompt_for_payment_method', [], function($u) use ($user) {
			$u->from('admin@jobgrouper.com');
			$u->to($user['email']);
			$u->subject('You haven\'t set a payment method');
		});
	}
    }
}
