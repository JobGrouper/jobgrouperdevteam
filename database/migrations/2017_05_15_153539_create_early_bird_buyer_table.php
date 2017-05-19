<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEarlyBirdBuyerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('early_bird_buyers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('employee_id')->unsigned();
            $table->integer('job_id')->unsigned();
	        $table->integer('sale_id')->unsigned();
            $table->enum('status', array('requested', 'denied', 'working', 'ended'))->default('requested');
            $table->timestamps();

	        // fks
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('users');
	        $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('job_id')->references('id')->on('jobs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
	Schema::drop('early_bird_buyers');
    }
}
