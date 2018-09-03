<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceTypeToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('service_type_id')->unsigned()->nullable()->after('client_id');

            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('packages_service_type_id_foreign')->unsigned()->nullable()->after('client_id');
            $table->dropColumn('service_type_id');
        });
    }
}
