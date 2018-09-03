<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentsToCheckpoints extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('office_zip')->nullable()->after('timezone_id');
            $table->string('office')->nullable()->after('timezone_id');
            $table->string('received_by')->nullable()->after('timezone_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn('office_zip');
            $table->dropColumn('office');
            $table->dropColumn('received_by');
        });
	}

}
