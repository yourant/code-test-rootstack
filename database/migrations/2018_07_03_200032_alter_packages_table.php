<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('sorting_gate_id')->nullable();

            $table->foreign('sorting_gate_id')->references('id')->on('sorting_gates')->onDelete('set null');
            $table->index('sorting_gate_id', 'packages_sorting_gate_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['sorting_gate_id']);
            $table->dropIndex('packages_sorting_gate_id_foreign');
            $table->dropColumn('sorting_gate_id');
        });
    }
}
