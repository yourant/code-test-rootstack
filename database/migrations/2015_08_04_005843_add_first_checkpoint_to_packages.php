<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstCheckpointToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('first_checkpoint_id')->unsigned()->nullable()->after('uploaded_by');
            $table->foreign('first_checkpoint_id')->references('id')->on('checkpoints')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('packages_first_checkpoint_id_foreign');
            $table->dropColumn('first_checkpoint_id');
        });
    }
}
