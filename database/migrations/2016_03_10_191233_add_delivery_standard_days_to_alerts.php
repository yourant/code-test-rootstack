<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryStandardDaysToAlerts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alerts', function(Blueprint $table) {
            $table->integer('delivery_standard_days')->unsigned()->nullable()->after('subtype');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alerts', function(Blueprint $table) {
            $table->dropColumn('delivery_standard_days');
        });
    }
}