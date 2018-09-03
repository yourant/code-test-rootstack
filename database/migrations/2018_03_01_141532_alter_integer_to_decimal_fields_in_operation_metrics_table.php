<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIntegerToDecimalFieldsInOperationMetricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_milestones', function(Blueprint $table) {
            $table->decimal('days', 3, 1)->nullable()->change();
            $table->decimal('warning1', 3, 1)->nullable()->change();
            $table->decimal('warning2', 3, 1)->nullable()->change();
            $table->decimal('critical1', 3, 1)->nullable()->change();
            $table->decimal('critical2', 3, 1)->nullable()->change();
            $table->decimal('critical3', 3, 1)->nullable()->change();
            $table->decimal('critical4', 3, 1)->nullable()->change();
        });

        Schema::table('operation_state_milestones', function(Blueprint $table) {
            $table->decimal('days', 3, 1)->nullable()->change();
            $table->decimal('warning1', 3, 1)->nullable()->change();
            $table->decimal('warning2', 3, 1)->nullable()->change();
            $table->decimal('critical1', 3, 1)->nullable()->change();
            $table->decimal('critical2', 3, 1)->nullable()->change();
            $table->decimal('critical3', 3, 1)->nullable()->change();
            $table->decimal('critical4', 3, 1)->nullable()->change();
        });

        Schema::table('operation_metrics', function(Blueprint $table) {
            $table->decimal('stalled', 5, 1)->nullable()->change();
            $table->decimal('segment', 5, 1)->nullable()->after('stalled');
            $table->decimal('controlled', 5, 1)->nullable()->change();
            $table->decimal('total', 5, 1)->nullable()->change();
        });

        Schema::table('operation_state_milestone_metrics', function(Blueprint $table) {
            $table->decimal('stalled', 5, 1)->nullable()->change();
            $table->decimal('controlled', 5, 1)->nullable()->change();
            $table->decimal('total', 5, 1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_milestones', function(Blueprint $table) {
            $table->integer('days')->unsigned()->nullable()->change();
            $table->integer('warning1')->unsigned()->nullable()->change();
            $table->integer('warning2')->unsigned()->nullable()->change();
            $table->integer('critical1')->unsigned()->nullable()->change();
            $table->integer('critical2')->unsigned()->nullable()->change();
            $table->integer('critical3')->unsigned()->nullable()->change();
            $table->integer('critical4')->unsigned()->nullable()->change();
        });

        Schema::table('operation_state_milestones', function(Blueprint $table) {
            $table->integer('days')->unsigned()->nullable()->change();
            $table->integer('warning1')->unsigned()->nullable()->change();
            $table->integer('warning2')->unsigned()->nullable()->change();
            $table->integer('critical1')->unsigned()->nullable()->change();
            $table->integer('critical2')->unsigned()->nullable()->change();
            $table->integer('critical3')->unsigned()->nullable()->change();
            $table->integer('critical4')->unsigned()->nullable()->change();
        });

        Schema::table('operation_metrics', function(Blueprint $table) {
            $table->integer('stalled')->unsigned()->nullable()->change();
            $table->dropColumn('segment');
            $table->integer('controlled')->unsigned()->nullable()->change();
            $table->integer('total')->unsigned()->nullable()->change();
        });

        Schema::table('operation_state_milestone_metrics', function(Blueprint $table) {
            $table->integer('stalled')->nullable()->change();
            $table->integer('controlled')->nullable()->change();
            $table->integer('total')->nullable()->change();
        });
    }
}
