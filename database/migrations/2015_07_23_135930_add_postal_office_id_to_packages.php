<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostalOfficeIdToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->integer('postal_office_id')->unsigned()->nullable()->after('distribution_center');

            $table->foreign('postal_office_id')->references('id')->on('postal_offices')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('packages_postal_office_id_foreign');
            $table->dropColumn('postal_office_id');
        });
    }
}
