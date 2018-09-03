<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClockstopCheckpointToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->integer('first_clockstop_id')->unsigned()->nullable()->after('last_checkpoint_id');

            $table->foreign('first_clockstop_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->dropIndex('packages_first_clockstop_id_foreign');
            $table->dropColumn('first_clockstop_id');
        });
    }
}
