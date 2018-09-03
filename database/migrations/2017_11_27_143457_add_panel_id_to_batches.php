<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPanelIdToBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_batches', function (Blueprint $table) {
            $table->integer('panel_id')->unsigned()->nullable()->after('frequency_id');

            $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_batches', function (Blueprint $table) {
            $table->dropForeign('operation_batches_panel_id_foreign');
            $table->dropColumn('panel_id');
        });
    }
}
