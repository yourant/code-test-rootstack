<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClockstopToCheckpointCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoint_codes', function(Blueprint $table) {
            $table->integer('clockstop')->unsigned()->default(0)->after('final');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoint_codes', function(Blueprint $table) {
            $table->dropColumn('clockstop');
        });
    }
}
