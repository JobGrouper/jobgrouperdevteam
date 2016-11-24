<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeCustomerSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_customer_sources', function (Blueprint $table) {
	    $table->char('id', 29)->primary();
	    $table->char('root_customer_id', 18);
	    $table->foreign('root_customer_id')->references('id')->on('stripe_root_customers')->onDelete('cascade');
	    $table->char('connected_customer_id', 18)->nullable();
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
        Schema::drop('stripe_customer_sources');
    }
}
