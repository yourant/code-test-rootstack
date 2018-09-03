<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInitialCheckpointCodeIdToAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->integer('initial_checkpoint_code_id')->unsigned()->nullable();
            $table->foreign('initial_checkpoint_code_id')->references('id')->on('checkpoint_codes')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropForeign('agreements_initial_checkpoint_code_id_foreign');
            $table->dropColumn('initial_checkpoint_code_id');
        });
    }
}
