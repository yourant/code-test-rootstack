<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStalledToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->boolean('stalled')->default(false)->after('canceled');
            $table->boolean('returning')->default(false)->after('stalled');
        });

        Schema::table('checkpoint_codes', function(Blueprint $table) {
            $table->boolean('stalled')->default(false)->after('canceled');
            $table->boolean('returning')->default(false)->after('stalled');

            $table->dropColumn('final');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoint_codes', function(Blueprint $table) {
            $table->dropColumn('stalled');
            $table->dropColumn('returning');

            $table->boolean('final')->default(false)->after('canceled');
        });

        Schema::table('packages', function(Blueprint $table) {
            $table->dropColumn('returning');
            $table->dropColumn('stalled');
        });
    }
}
