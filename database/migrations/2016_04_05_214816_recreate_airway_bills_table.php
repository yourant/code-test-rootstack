<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateAirwayBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('air_waybills', function(Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->dateTime('departed_at')->nullable();
            $table->dateTime('arrived_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('details')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('dispatches', function(Blueprint $table) {
            $table->integer('air_waybill_id')->unsigned()->nullable()->after('agreement_id');

            $table->foreign('air_waybill_id')->references('id')->on('air_waybills')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatches', function(Blueprint $table) {
            $table->dropForeign('dispatches_air_waybill_id_foreign');
            $table->dropColumn('air_waybill_id');
        });

        Schema::drop('air_waybills');
    }
}
