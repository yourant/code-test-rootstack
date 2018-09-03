<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdmissionsRefactorPostal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();
            
            logger("Start postal migration");

            // Rename locations tables
            // ------------------------------------------------------------------------------------------------
            
            logger("Rename location tables");
            
            Schema::rename('states', 'admin_level_1');
            Schema::rename('towns', 'admin_level_2');
            Schema::rename('townships', 'admin_level_3');

            // Renombrar tabla township_types
            Schema::rename('township_types', 'admin_level_3_types');

            // Foreign Keys of the towns and townships tables
            Schema::table('admin_level_2', function (Blueprint $table) {
                $table->dropForeign('towns_state_id_foreign');
                $table->renameColumn('state_id', 'admin_level_1_id');
                $table->foreign('admin_level_1_id')->references('id')->on('admin_level_1')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('admin_level_3', function (Blueprint $table) {
                $table->dropForeign('townships_town_id_foreign');
                $table->renameColumn('town_id', 'admin_level_2_id');
                $table->foreign('admin_level_2_id')->references('id')->on('admin_level_2')->onUpdate('cascade')->onDelete('cascade');
            });

            // Foreign Keys of the zip_codes table
            Schema::table('zip_codes', function (Blueprint $table) {
                $table->dropForeign('zip_codes_township_id_foreign');
                $table->dropForeign('zip_codes_township_type_id_foreign');

                $table->renameColumn('township_id', 'admin_level_3_id');
                $table->renameColumn('township_type_id', 'admin_level_3_type_id');

                $table->foreign('admin_level_3_id')->references('id')->on('admin_level_3')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('admin_level_3_type_id')->references('id')->on('admin_level_3_types')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('operation_state_milestones', function (Blueprint $table) {
                $table->dropForeign('operation_state_milestones_state_id_foreign');
                $table->renameColumn('state_id', 'admin_level_1_id');
                $table->foreign('admin_level_1_id')->references('id')->on('admin_level_1')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('postal_offices', function (Blueprint $table) {
                $table->dropForeign('postal_offices_township_id_foreign');
                $table->renameColumn('township_id', 'admin_level_3_id');
                $table->foreign('admin_level_3_id')->references('id')->on('admin_level_3')->onUpdate('cascade')->onDelete('cascade');
            });

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            logger($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
