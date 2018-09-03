<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckpointCodeAlertsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->string('name');
        });

        Schema::create('checkpoint_code_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('classification_id')->unsigned();
            $table->integer('checkpoint_code_id')->unsigned();

            $table->foreign('classification_id')->references('id')->on('classifications')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('subtype')->nullable();
        });

        Schema::create('alert_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alert_id')->unsigned();
            $table->integer('classification_id')->unsigned();
            $table->integer('days')->unsigned()->default(0);

            $table->foreign('alert_id')->references('id')->on('alerts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('classification_id')->references('id')->on('classifications')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('alert_classification');
        Schema::drop('alerts');
        Schema::drop('checkpoint_code_classification');
        Schema::drop('classifications');
    }
}
