<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('buyer_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->integer('month')->unsigned();
            $table->float('amount')->unsigned();
            $table->enum('payment_system', array('paypal', 'stripe'));
            $table->string('status');

            $table->timestamps();

            $table->foreign('buyer_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('sales')->onDeleete('cascade'); //todo cascade
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payments');
    }
}
