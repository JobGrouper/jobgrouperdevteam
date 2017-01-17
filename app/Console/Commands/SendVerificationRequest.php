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
	
	// verification requests will have been created
	$users = $json_file['unverified_with_account'];

	foreach($users as $user) {

		Mail::send('emails.prompt_for_verification', [], function($u) use ($user) {
			$u->from('admin@jobgrouper.com');
			$u->to($user['email']);
			$u->subject('We need some more information from you');
		}
	}
    }
}
