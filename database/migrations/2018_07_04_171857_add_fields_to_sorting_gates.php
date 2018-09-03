<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToSortingGates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sorting_gates', function (Blueprint $table) {
            $table->renameColumn('gate_number', 'number');
            $table->renameColumn('gate_code', 'name');
            $table->string('code', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sorting_gates', function (Blueprint $table) {
            $table->renameColumn('number', 'gate_number');
            $table->renameColumn('name', 'gate_code');
            $table->dropColumn('code');
        });
    }
}
