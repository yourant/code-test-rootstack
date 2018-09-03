<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortingIdToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedInteger('sorting_id')->nullable();

            $table->foreign('sorting_id')->references('id')->on('sortings')->onUpdate('cascade')->onDelete('set null');
            $table->index('sorting_id', 'services_sorting_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['sorting_id']);
            $table->dropColumn('sorting_id');
        });
    }
}
