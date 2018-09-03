<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function(Blueprint $table) {
            $table->dateTime('first_checkpoint_at')->nullable()->after('first_checkpoint_id');
            $table->dateTime('last_checkpoint_at')->nullable()->after('last_checkpoint_id');
            $table->dateTime('first_clockstop_at')->nullable()->after('first_clockstop_id');
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
            $table->dropColumn('first_checkpoint_at');
            $table->dropColumn('last_checkpoint_at');
            $table->dropColumn('first_clockstop_at');
        });
    }
}
