<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifiedWerightToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->decimal('verified_weight', 8, 3)->nullable()->after('weight');

            $table->decimal('weight', 8, 3)->default(0)->change();
            $table->decimal('net_weight', 8, 3)->default(0)->change();
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
            $table->decimal('weight', 8, 2)->default(0)->change();
            $table->decimal('net_weight', 8, 2)->default(0)->change();

            $table->dropColumn('verified_weight', 8, 3);
        });
    }
}
