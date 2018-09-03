<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstControlledCheckpointToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('first_controlled_checkpoint_id')->unsigned()->nullable()->after('last_checkpoint_at');
            $table->dateTime('first_controlled_checkpoint_at')->nullable()->after('first_controlled_checkpoint_id');
            $table->foreign('first_controlled_checkpoint_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('set null');

            $table->dropForeign('packages_initial_checkpoint_id_foreign');
            $table->dropColumn('initial_checkpoint_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {

            $table->integer('initial_checkpoint_id')->unsigned()->nullable()->after('first_clockstop_id');
            $table->foreign('initial_checkpoint_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('set null');

            $table->dropForeign('package_first_controlled_checkpoint_id_foreign');
            $table->dropColumn('first_controlled_checkpoint_id');
            $table->dropColumn('first_controlled_checkpoint_at');
        });
    }
}
