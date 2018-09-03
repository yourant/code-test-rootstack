<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderToCheckpointCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoint_codes', function (Blueprint $table) {
            $table->integer('provider_id')->unsigned()->nullable()->after('id');

            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoint_codes', function (Blueprint $table) {
            $table->dropForeign('checkpoint_codes_provider_id_foreign');

            $table->dropColumn('provider_id');
        });
    }
}
