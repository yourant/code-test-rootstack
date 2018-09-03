<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProviderToPostalOffices extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postal_offices', function (Blueprint $table) {
            $table->integer('provider_id')->unsigned()->nullable()->after('id');

            $table->foreign('provider_id')->references('id')->on('providers')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('postal_offices', function (Blueprint $table) {
            $table->dropForeign('postal_offices_provider_id_foreign');
            $table->dropColumn('provider_id');
        });
    }
}
