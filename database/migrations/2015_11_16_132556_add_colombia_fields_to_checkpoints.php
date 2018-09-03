<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColombiaFieldsToCheckpoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->string('city')->after('office_zip')->nullable();
            $table->string('liquidacion')->after('city')->nullable();
        });

        Schema::table('checkpoint_codes', function (Blueprint $table) {
            $table->integer('code')->nullable()->change();

            $table->dropUnique('checkpoint_codes_type_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn('city')->after('office_zip')->nullable();
            $table->dropColumn('liquidacion')->after('city')->nullable();
        });

        Schema::table('checkpoint_codes', function (Blueprint $table) {
            $table->integer('code')->nullable()->change();

            $table->unique(['type', 'code']);
        });
    }
}
