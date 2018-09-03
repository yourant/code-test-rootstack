<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameLiquidacionFromCheckpoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoints', function(Blueprint $table) {
            $table->renameColumn('liquidacion', 'details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoints', function(Blueprint $table) {
            $table->renameColumn('details', 'liquidacion');
        });
    }
}
