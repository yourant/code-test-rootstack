<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToAgreements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->enum('type', ['standard', 'priority'])->default('standard')->after('transit_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements', function(Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
