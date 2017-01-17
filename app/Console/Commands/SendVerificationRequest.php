<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use DB;

class SendVerificationRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'op:prompt_for_verification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time command to prompt employees for verification';

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
		throw new \Exception('SendVerificationRequest:: the account file couldn\'t be opened');
	}

	// verification requests will have been created
	$users = $json_file['unverified_with_account'];

	foreach($users as $user) {

		if (!isset($user['request_id'])) {
			throw new \Exception('SendVerificationRequest:: no request id has been set for ' . $user['email']);
		}

		Mail::send('emails.seller_need_additional_verification', ['request_id' => $user['request_id']], function($u) use ($user) {
			$u->from('admin@jobgrouper.com');
			$u->to($user['email']);
			$u->subject('We need some more information from you');
		});
	}
    }
}
