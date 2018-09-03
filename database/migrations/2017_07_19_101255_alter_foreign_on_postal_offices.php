<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterForeignOnPostalOffices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('postal_offices', function (Blueprint $table) {
            $table->dropForeign('postal_offices_township_id_foreign');
            $table->dropForeign('postal_offices_postal_office_type_id_foreign');

            $table->foreign('township_id')->references('id')->on('townships')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('postal_office_type_id')->references('id')->on('postal_office_types')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropForeign('postal_offices_township_id_foreign');
            $table->dropForeign('postal_offices_postal_office_type_id_foreign');

            $table->foreign('township_id')->references('id')->on('townships')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('postal_office_type_id')->references('id')->on('postal_office_types')->onUpdate('cascade')->onDelete('restrict');
        });
    }
}
