<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVolumetricFieldsToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->decimal('vol_weight', 8, 3)->nullable()->after('verified_weight');
            $table->decimal('verified_vol_weight', 8, 3)->nullable()->after('vol_weight');

            $table->decimal('verified_width')->nullable()->after('width');
            $table->decimal('verified_height')->nullable()->after('height');
            $table->decimal('verified_length')->nullable()->after('length');
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
            $table->dropColumn('vol_weight');
            $table->dropColumn('verified_vol_weight');

            $table->dropColumn('verified_width');
            $table->dropColumn('verified_height');
            $table->dropColumn('verified_length');
        });
    }
}
