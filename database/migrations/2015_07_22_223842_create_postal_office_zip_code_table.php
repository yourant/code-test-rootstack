<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostalOfficeZipCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_office_zip_code', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('postal_office_id')->unsigned();
            $table->integer('zip_code_id')->unsigned();

            $table->foreign('postal_office_id')->references('id')->on('postal_offices')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('zip_code_id')->references('id')->on('zip_codes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('postal_office_zip_code');
    }
}
