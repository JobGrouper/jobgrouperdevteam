<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyerAdjustmentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_adjustment_requests', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('job_id')->unsigned();
            $table->integer('employee_id')->unsigned();
            $table->integer('current_client_min')->unsigned();
            $table->integer('current_client_max')->unsigned();
            $table->integer('requested_client_min')->unsigned();
            $table->integer('requested_client_max')->unsigned();
            $table->enum('status', ['pending', 'accepted', 'denied'])->default('pending');
            $table->timestamp('decision_date')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('job_id')->references('id')->on('jobs');
            $table->foreign('employee_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('buyer_adjustment_requests');
    }
}
