<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameBatchOnStateMilestoneMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_state_milestone_metrics', function (Blueprint $table) {
            $table->dropForeign('operation_state_milestone_metrics_state_batch_id_foreign');
            $table->dropColumn('state_batch_id');
            $table->dropColumn('created_at');

            $table->integer('batch_id')->unsigned()->nullable()->after('id');
            $table->foreign('batch_id')->references('id')->on('operation_batches')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('operation_state_milestone_metrics', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::drop('operation_state_batches');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // State Batches
        Schema::create('operation_state_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('frequency_id')->unsigned();
            $table->string('value');
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();

            $table->foreign('frequency_id')->references('id')->on('operation_frequencies')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('operation_state_milestone_metrics', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('operation_state_milestone_metrics', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('package_id');
            $table->dropForeign('operation_state_milestone_metrics_batch_id_foreign');
            $table->dropColumn('batch_id');

            $table->integer('state_batch_id')->unsigned()->nullable()->after('id');
            $table->foreign('state_batch_id')->references('id')->on('operation_state_batches')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}
