<?php

use Illuminate\Database\Migrations\Migration;

class AdmissionsRefactorFixForeigns extends Migration
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

            DB::statement('ALTER TABLE admin_level_1 RENAME CONSTRAINT states_country_id_foreign TO admin_level_1_country_id_foreign');
            DB::statement('ALTER TABLE admin_level_1 RENAME CONSTRAINT states_region_id_foreign TO admin_level_1_region_id_foreign');
            DB::statement('ALTER TABLE admin_level_2 RENAME CONSTRAINT towns_region_id_foreign TO admin_level_2_region_id_foreign');
            DB::statement('ALTER TABLE provider_services RENAME CONSTRAINT service_types_first_checkpoint_code_id_foreign TO provider_services_first_checkpoint_code_id_foreign');
            DB::statement('ALTER TABLE provider_services RENAME CONSTRAINT service_types_last_checkpoint_code_id_foreign TO provider_services_last_checkpoint_code_id_foreign');
            DB::statement('ALTER TABLE provider_services RENAME CONSTRAINT service_types_provider_id_foreign TO provider_services_provider_id_foreign');
            DB::statement('ALTER TABLE provider_services RENAME CONSTRAINT service_types_provider_service_type_id_foreign TO provider_services_provider_service_type_id_foreign');

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            logger($e->getTraceAsString());
            throw new Exception($e->getMessage());
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
