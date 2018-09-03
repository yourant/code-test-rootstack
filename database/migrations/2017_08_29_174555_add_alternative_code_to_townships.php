<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlternativeCodeToTownships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('townships', function (Blueprint $table) {
            $table->string('territorial_code')->nullable()->after('town_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('townships', function (Blueprint $table) {
            $table->dropColumn('territorial_code');
        });
    }
}
