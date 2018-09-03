<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewArchivedColumnPrealerts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prealerts', function (Blueprint $table) {
            $table->boolean('archived')->default(false)->after('errors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prealerts', function (Blueprint $table) {
            $table->dropColumn('archived');
        });
    }
}
