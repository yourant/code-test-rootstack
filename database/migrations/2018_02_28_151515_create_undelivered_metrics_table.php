<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUndeliveredMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_batches', function(Blueprint $table) {
            $table->boolean('archived')->default(false)->after('total');
        });

        Schema::create('operation_undelivered', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('batch_id')->unsigned();
            $table->integer('total')->unsigned();
            $table->integer('critical')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('operation_batches')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('operation_undelivered_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('undelivered_id')->unsigned();
            $table->integer('segment_id')->unsigned();
            $table->integer('total')->unsigned();
            $table->integer('critical')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('undelivered_id')->references('id')->on('operation_undelivered')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('segment_id')->references('id')->on('operation_segments')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('operation_undelivered_state_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('undelivered_id')->unsigned();
            $table->integer('state_milestone_id')->unsigned();
            $table->integer('total')->unsigned();
            $table->integer('critical')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('undelivered_id')->references('id')->on('operation_undelivered')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('state_milestone_id')->references('id')->on('operation_state_milestones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('operation_undelivered_state_metrics');
        Schema::drop('operation_undelivered_metrics');
        Schema::drop('operation_undelivered');

        Schema::table('operation_batches', function(Blueprint $table) {
            $table->dropColumn('archived');
        });
    }
}