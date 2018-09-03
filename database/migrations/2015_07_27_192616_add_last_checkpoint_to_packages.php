<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastCheckpointToPackages extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('last_checkpoint_id')->unsigned()->nullable()->after('uploaded_by');
            $table->foreign('last_checkpoint_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('packages_last_checkpoint_id_foreign');
            $table->dropColumn('last_checkpoint_id');
        });
    }
}
