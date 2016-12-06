<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeVerificationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_verification_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->char('managed_account_id', 21);
            $table->text('fields_needed');
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->foreign('managed_account_id')->references('id')->on('stripe_managed_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stripe_verification_requests');
    }
}
