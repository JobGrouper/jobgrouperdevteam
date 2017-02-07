<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyerAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_adjustments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id')->unsigned();
            $table->integer('old_client_max')->unsigned();
            $table->integer('old_client_min')->unsigned();
            $table->integer('new_client_max')->unsigned();
            $table->integer('new_client_min')->unsigned();
            $table->integer('from_request_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('users');
            $table->foreign('from_request_id')->references('id')->on('buyer_adjustment_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('buyer_adjustments');
    }
}
