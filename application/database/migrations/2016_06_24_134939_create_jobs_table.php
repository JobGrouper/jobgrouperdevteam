<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('employee_id')->unsigned()->nullable();
            $table->string('title',255);
            $table->text('description');
            $table->float('salary')->unsigned();
            $table->integer('max_clients_count')->unsigned();
            $table->boolean('hot');
            $table->enum('status', array('waiting', 'working'))->default('waiting');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::drop('jobs');
    }
}
