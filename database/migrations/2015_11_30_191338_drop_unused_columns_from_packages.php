<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnusedColumnsFromPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign('packages_client_id_foreign');
            $table->dropForeign('packages_service_type_id_foreign');

            $table->dropColumn('client_id');
            $table->dropColumn('service_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Non reversible
    }
}
