<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAirwaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('air_waybills', function (Blueprint $table) {
            $table->dateTime('checked_in_at')->nullable()->after('code');
            $table->dateTime('confirmed_at')->nullable()->after('arrived_at');
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
            $table->dropColumn('checked_in_at');
            $table->dropColumn('confirmed_at');
        });
    }
}
