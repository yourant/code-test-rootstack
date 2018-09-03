<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryToProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function(Blueprint $table) {
            $table->integer('country_id')->unsigned()->nullable()->after('name');
            $table->integer('timezone_id')->unsigned()->nullable()->after('country_id');

            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('timezone_id')->references('id')->on('timezones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function(Blueprint $table) {
            $table->dropForeign('providers_country_id_foreign');
            $table->dropForeign('providers_timezone_id_foreign');

            $table->dropColumn('country_id');
            $table->dropColumn('timezone_id');
        });
    }
}
