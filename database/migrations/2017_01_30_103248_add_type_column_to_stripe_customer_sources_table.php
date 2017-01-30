<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColumnToStripeCustomerSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stripe_customer_sources', function ($table) {
            $table->enum('type', array('bank_account', 'card'))->default('card');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stripe_customer_sources', function ($table) {
            $table->dropColumn('type');
        });
    }
}
