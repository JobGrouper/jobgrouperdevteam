<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingStatusToSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sales', function ($table) {
		$table->dropColumn('status');
	});

	Schema::table('sales', function ($table) {
		$table->enum('status', array('pending', 'in_progress', 'closed'));
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
        Schema::table('sales', function ($table) {
		$table->dropColumn('status');
	});

        Schema::table('sales', function ($table) {
		$table->enum('status', array('in_progress','closed'));
	});
    }
}
