<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Это было для сохранения карт и авто-оплат
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned();
            $table->string('card_id');
            $table->string('valid_until');
            $table->string('type');
            $table->string('number');
            $table->integer('expire_month');
            $table->integer('expire_year');
            $table->string('first_name');
            $table->string('last_name');

            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::drop('credit_cards');
    }
}
