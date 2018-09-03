<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanceledToCheckpointCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoint_codes', function(Blueprint $table){
            $table->boolean('delivered')->default(false)->after('description_en');
            $table->boolean('returned')->default(false)->after('delivered');
            $table->boolean('canceled')->default(false)->after('returned');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoint_codes', function(Blueprint $table){
            $table->dropColumn('delivered');
            $table->dropColumn('returned');
            $table->dropColumn('canceled');
        });
    }
}
