<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeConnectedCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_connected_customers', function (Blueprint $table) {
	    $table->char('id', 18)->primary();
	    $table->char('root_customer_id')->nullable();
	    $table->foreign('root_customer_id')->references('id')->on('stripe_root_customers')->onDelete('cascade');
	    $table->integer('user_id')->unsigned();
	    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::drop('stripe_connected_customers');
    }
}
