<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCloseOrderRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('close_order_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('originator_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->string('reason');
            $table->enum('status', array('pending', 'approved', 'rejected'))->default('pending');
            $table->timestamps();

            $table->foreign('originator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('close_order_requests');
    }
}
