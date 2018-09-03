<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckpointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('checkpoints', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->integer('checkpoint_code_id')->unsigned()->nullable();
            $table->dateTime('checkpoint_at');
			$table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('set null');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('checkpoints');
	}

}
