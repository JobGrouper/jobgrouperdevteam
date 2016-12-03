<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_invoices', function (Blueprint $table) {
	    $table->char('id', 27)->primary();
	    $table->char('subscription_id', 18);
	    $table->foreign('subscription_id')->references('id')->on('stripe_subscriptions')->onDelete('cascade');
	    $table->char('connected_customer_id', 18);
	    $table->foreign('connected_customer_id')->references('id')->on('stripe_connected_customers')->onDelete('cascade');
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
        Schema::drop('stripe_invoices');
    }
}
