<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimezoneToCheckpoint extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoints', function (Blueprint $table)
        {
            $table->integer('timezone_id')->unsigned()->nullable()->after('checkpoint_code_id');

            $table->foreign('timezone_id')->references('id')->on('timezones')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoints', function (Blueprint $table)
        {
            $table->dropForeign('checkpoints_timezone_id_foreign');
            $table->dropColumn('timezone_id');
        });
    }
}
