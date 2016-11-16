<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeExternalAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_external_accounts', function (Blueprint $table) {
	    $table->char('id', 27)->primary();
	    $table->char('managed_account_id', 21);
	    $table->foreign('managed_account_id')->references('id')->on('stripe_managed_accounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stripe_external_accounts');
    }
}
