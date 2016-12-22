<?php

namespace App\Console\Commands;

use App\StripeVerificationRequest;

use Illuminate\Console\Command;
use Faker\Factory as Faker;
use DB;

class GenerateStripeVerificationRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:stripe_verification_request {user_id}';

    protected $fields;

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

	$this->fields = [
            'legal_entity.address.city',
            'legal_entity.address.line1',
            'legal_entity.address.postal_code',
            'legal_entity.address.state',
            //'legal_entity.business_name',
            //'legal_entity.business_tax_id',
            'legal_entity.dob.day',
            'legal_entity.dob.month',
            'legal_entity.dob.year',
            'legal_entity.first_name',
            'legal_entity.last_name',
            'legal_entity.ssn_last_4',
            'legal_entity.type',
            'legal_entity.personal_id_number'
	    ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
	$user_id = $this->argument('user_id');
	$account_record = DB::table('stripe_managed_accounts')->
		where('user_id', $user_id)->first();

	$random_fields = array();
	foreach ($this->fields as $field) {

		if (rand(0,1) == 1) {
			array_push($random_fields, $field);
		}
	}
	StripeVerificationRequest::create([
	    'managed_account_id' => $account_record->id,
	    'fields_needed' => json_encode($random_fields),
	]);
    }
}
