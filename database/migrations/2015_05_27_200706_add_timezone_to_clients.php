<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimezoneToClients extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clients', function(Blueprint $table) {
            $table->integer('timezone_id')->unsigned()->after('id');
            $table->dropColumn('timezone');

            $table->foreign('timezone_id')->references('id')->on('timezones')->onUpdate('cascade')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('clients', function(Blueprint $table) {
            $table->dropForeign('clients_timezone_id_foreign');
            $table->dropColumn('timezone_id');

            $table->string('timezone')->after('country');
        });
	}

}
