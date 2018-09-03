<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOperationPerformanceStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_performance_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('panel_id')->unsigned();
            $table->integer('processed')->unsigned()->default(0);
            $table->integer('total')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('operation_performances', function (Blueprint $table) {
            $table->dropForeign('operation_performances_panel_id_foreign');
            $table->dropColumn('panel_id');

            $table->integer('batch_id')->unsigned()->nullable()->after('id');

            $table->foreign('batch_id')->references('id')->on('operation_performance_batches')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('operation_performance_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('performance_id')->unsigned();
            $table->integer('package_id')->unsigned();
            $table->decimal('clockstop', 5, 1)->nullable();
            $table->decimal('delivered', 5, 1)->nullable();
            $table->decimal('controlled', 5, 1)->nullable();
            $table->timestamps();

            $table->foreign('performance_id')->references('id')->on('operation_performances')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::drop('operation_performance_metrics');

        Schema::table('operation_performances', function (Blueprint $table) {
            $table->dropForeign('operation_performances_batch_id_foreign');
            $table->dropColumn('batch_id');
            $table->integer('panel_id')->unsigned()->nullable()->after('id');

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::drop('operation_performance_batches');
    }
}
