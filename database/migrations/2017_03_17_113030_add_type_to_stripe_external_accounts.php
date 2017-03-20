<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToStripeExternalAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // STRIPE CONNECTED CUSTOMER
	Schema::table('stripe_external_accounts', function ($table) {
		$table->enum('account_type', array('card', 'bank_account'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('stripe_external_accounts', function ($table) {
                $table->dropColumn('account_type');
        });
    }
}
