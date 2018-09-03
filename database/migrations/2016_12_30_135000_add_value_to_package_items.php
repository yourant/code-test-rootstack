<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValueToPackageItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_items', function(Blueprint $table) {
            $table->decimal('value')->nullable()->after('quantity');
            $table->decimal('net_weight', 8, 3)->nullable()->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_items', function(Blueprint $table) {
            $table->dropColumn('value');
            $table->dropColumn('net_weight');
        });
    }
}
