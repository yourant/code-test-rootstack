<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->smallInteger('position')->unsigned()->default(0);
        });

        Schema::create('boundaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('segment_id')->unsigned();
            $table->integer('lower')->unsigned();
            $table->integer('upper')->unsigned()->nullable();

            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->smallInteger('position')->unsigned()->default(0);
        });

        Schema::create('milestone_segment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('milestone_id')->unsigned();
            $table->integer('segment_id')->unsigned();

            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('milestone_id')->references('id')->on('milestones')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('checkpoint_code_milestone', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('checkpoint_code_id')->unsigned();
            $table->integer('milestone_id')->unsigned();

            $table->foreign('checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('milestone_id')->references('id')->on('milestones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('checkpoint_code_milestone');
        Schema::drop('milestone_segment');
        Schema::drop('milestones');
        Schema::drop('boundaries');
        Schema::drop('segments');
    }
}
