<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubaccountToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->integer('marketplace_id')->unsigned()->nullable()->after('leg_id');

            $table->foreign('marketplace_id')->references('id')->on('marketplaces')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('packages_marketplace_id_foreign');

            $table->dropColumn('marketplace_id');
        });
    }
}
