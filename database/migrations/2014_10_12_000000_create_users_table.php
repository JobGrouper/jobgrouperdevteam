a<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()

    {

        Schema::create('users', function (Blueprint $table)
        {  
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('user_type', array('buyer', 'employee'));
            $table->enum('role', array('user', 'admin'))->default('user');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->boolean('email_confirmed')->default(0);
            $table->string('linkid_url');
            $table->string('fb_url');
            $table->string('git_url');
            $table->string('description', 550);
            $table->boolean('active')->default(true);
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
