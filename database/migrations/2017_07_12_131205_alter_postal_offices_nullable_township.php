<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPostalOfficesNullableTownship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postal_offices', function(Blueprint $table) {
            $table->string('code', 10)->change();
            $table->integer('township_id')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postal_offices', function(Blueprint $table) {
            $table->integer('township_id')->unsigned()->change();
            $table->string('code', 5)->change();
        });
    }
}
