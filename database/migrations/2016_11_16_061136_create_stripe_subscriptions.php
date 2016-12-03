<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_subscriptions', function (Blueprint $table) {
	    $table->char('id', 18)->primary();
	    $table->string('plan_id', 40);
	    $table->foreign('plan_id')->references('id')->on('stripe_plans')->onDelete('cascade');
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
        Schema::drop('stripe_subscriptions');
    }
}
