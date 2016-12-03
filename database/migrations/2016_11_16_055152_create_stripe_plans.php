<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_plans', function (Blueprint $table) {
            $table->string('id', 40)->primary();
	    $table->char('managed_account_id', 21);
	    $table->foreign('managed_account_id')->references('id')->on('stripe_managed_accounts')->onDelete('cascade');
	    $table->integer('job_id')->unsigned();
	    $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
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
        Schema::drop('stripe_plans');
    }
}
