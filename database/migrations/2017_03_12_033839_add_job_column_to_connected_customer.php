<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJobColumnToConnectedCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // STRIPE CONNECTED CUSTOMER
	Schema::table('stripe_connected_customers', function ($table) {
	    $table->integer('job_id')->unsigned();
	    $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
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
        Schema::table('stripe_connected_customers', function ($table) {
                $table->dropColumn('job_id');
        });
    }
}
