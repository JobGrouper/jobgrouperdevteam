<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToStripeSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// STRIPE CONNECTED CUSTOMER
		 Schema::table('stripe_connected_customers', function ($table) {
			$table->char('managed_account_id', 21);
			$table->foreign('managed_account_id')->references('id')->on('stripe_managed_accounts')->onDelete('cascade');
		});       //

		 // STRIPE PLANS
		Schema::table('stripe_plans', function ($table) {
			$table->boolean('activated');
		});       

		// STRIPE SUBSCRIPTIONS
		Schema::table('stripe_subscriptions', function ($table) {
			$table->boolean('activated');
		});       //

		// STRIPE CUSTOMER SOURCES
		Schema::table('stripe_customer_sources', function ($table) {
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
        Schema::table('stripe_connected_customers', function ($table) {
                $table->dropColumn('managed_account_id');
        });

            //
        Schema::table('stripe_plans', function ($table) {
                $table->dropColumn('activated');
        });

            //
        Schema::table('stripe_subscriptions', function ($table) {
                $table->dropColumn('activated');
        });

            //
        Schema::table('stripe_customer_sources', function ($table) {
                $table->dropColumn('last_four');
        });
    }
}
