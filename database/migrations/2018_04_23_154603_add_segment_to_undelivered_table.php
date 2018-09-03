<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSegmentToUndeliveredTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_undelivered_metrics', function (Blueprint $table) {
            $table->integer('segment')->unsigned()->nullable()->after('total');
        });

        Schema::table('operation_undelivered_state_metrics', function (Blueprint $table) {
            $table->integer('segment')->unsigned()->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_undelivered_metrics', function(Blueprint $table) {
            $table->dropColumn('segment');
        });

        Schema::table('operation_undelivered_state_metrics', function(Blueprint $table) {
            $table->dropColumn('segment');
        });
    }
}
