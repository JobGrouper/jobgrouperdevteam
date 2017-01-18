<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use Mail;

class VerifyEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'op:verify_employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies employees read from a generated list';

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
        //
        // Load file
	$json_file = json_decode(file_get_contents('storage/files/preexisting-accounts.json'), True);

	if (!$json_file) {
		throw new \Exception('VerifyEmployees:: the account file couldn\'t be opened');
	}

	// verification requests will have been created
	$users = $json_file['verified_with_account'];
	$emails = array();

	foreach ($users as $user) {
		array_push($emails, $user['email']);
	}

	DB::table('users')->whereIn('email', $emails)->
		update(['verified' => 1]);
    }
}
