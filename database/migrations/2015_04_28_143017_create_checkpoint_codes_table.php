<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckpointCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('checkpoint_codes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type', 6);
            $table->integer('code');
            $table->string('description');
            $table->boolean('final')->default(0);

            $table->unique(['type', 'code']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('checkpoint_codes');
	}

}
