<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zip_codes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('township_id')->unsigned();
            $table->integer('township_type_id')->unsigned();

//            $table->unique(['code', 'town_id', 'township', 'township_type_id']);
            $table->foreign('township_id')->references('id')->on('townships')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('township_type_id')->references('id')->on('township_types')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('zip_codes');
    }
}
