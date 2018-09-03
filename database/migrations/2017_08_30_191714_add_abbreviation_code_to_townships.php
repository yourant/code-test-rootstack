<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAbbreviationCodeToTownships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('townships', function (Blueprint $table) {
            $table->string('abbreviation_code', 10)->nullable()->after('territorial_code');
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
            $table->dropColumn('abbreviation_code');
        });
    }
}
