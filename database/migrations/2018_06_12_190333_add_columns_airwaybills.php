<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAirwaybills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('air_waybills', function (Blueprint $table) {
            $table->integer('origin_airport_id')->unsigned()->nullable();
            $table->integer('destination_airport_id')->unsigned()->nullable();

            $table->foreign('origin_airport_id')->references('id')->on('airports')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('destination_airport_id')->references('id')->on('airports')->onUpdate('cascade')->onDelete('cascade');

            $table->index('origin_airport_id', 'air_waybills_origin_airport_id_foreign');
            $table->index('destination_airport_id', 'air_waybills_destination_airport_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('air_waybills', function (Blueprint $table) {
            $table->dropColumn('origin_airport_id');
            $table->dropColumn('destination_airport_id');
        });
    }
}
