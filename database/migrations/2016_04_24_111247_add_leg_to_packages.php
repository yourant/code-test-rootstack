<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLegToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->integer('leg_id')->unsigned()->nullable()->after('bag_id');

            $table->foreign('leg_id')->references('id')->on('legs')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('packages_leg_id_foreign');
            $table->dropColumn('leg_id');
        });
    }
}
