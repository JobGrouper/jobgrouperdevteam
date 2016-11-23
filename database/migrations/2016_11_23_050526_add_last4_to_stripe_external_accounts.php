<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLast4ToStripeExternalAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	// STRIPE CUSTOMER SOURCES
	Schema::table('stripe_external_accounts', function ($table) {
		$table->char('last_four', 4);
	});       //
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
    		$table->dropColumn('last_four');
	});
    }
}
