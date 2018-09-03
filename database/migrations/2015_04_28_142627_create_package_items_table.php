<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('package_items', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->string('part_no')->nullable();
            $table->string('description');
            $table->integer('quantity')->unsigned();
            $table->string('hs_code');
			$table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onUpdate('cascade')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('package_items');
	}

}
