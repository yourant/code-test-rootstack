<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostalOfficesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_office_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('postal_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('township_id')->unsigned();
            $table->integer('postal_office_type_id')->unsigned();
            $table->integer('code');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('email')->nullable();
            $table->string('reference')->nullable();

            $table->foreign('township_id')->references('id')->on('townships')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('postal_office_type_id')->references('id')->on('postal_office_types')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('postal_offices');
        Schema::drop('postal_office_types');
    }
}
