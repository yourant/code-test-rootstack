<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdAndGenericToProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('providers', function (Blueprint $table) {

            $table->boolean('generic')->default(false)->after('timezone_id');

            $table->integer('parent_id')->unsigned()->nullable()->after('generic');

            $table->foreign('parent_id')
            ->references('id')
            ->on('providers')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropForeign('providers_parent_id_foreign');
            $table->dropColumn('parent_id');
            $table->dropColumn('generic');
        });
    }
}
