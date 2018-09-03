<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AdmissionsRefactorFixIndexes extends Migration
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

            DB::statement('ALTER INDEX IF EXISTS states_country_id_foreign RENAME TO admin_level_1_country_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS states_pkey RENAME TO admin_level_1_pkey');
            DB::statement('ALTER INDEX IF EXISTS states_region_id_foreign RENAME TO admin_level_1_region_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS towns_pkey RENAME TO admin_level_2_pkey');
            DB::statement('ALTER INDEX IF EXISTS towns_region_id_foreign RENAME TO admin_level_2_region_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS towns_state_id_foreign RENAME TO admin_level_2_admin_level_1_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS townships_pkey RENAME TO admin_level_3_pkey');
            DB::statement('ALTER INDEX IF EXISTS townships_town_id_foreign RENAME TO admin_level_3_admin_level_2_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS township_types_pkey RENAME TO admin_level_3_types_pkey');
            DB::statement('ALTER INDEX IF EXISTS postal_offices_township_id_foreign RENAME TO postal_offices_admin_level_3_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS service_types_first_checkpoint_code_id_foreign RENAME TO provider_services_first_checkpoint_code_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS service_types_last_checkpoint_code_id_foreign RENAME TO provider_services_last_checkpoint_code_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS service_types_provider_id_foreign RENAME TO provider_services_provider_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS service_types_provider_service_type_id_foreign RENAME TO provider_services_provider_service_type_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS zip_codes_township_id_foreign RENAME TO zip_codes_admin_level_3_id_foreign');
            DB::statement('ALTER INDEX IF EXISTS zip_codes_township_type_id_foreign RENAME TO zip_codes_admin_level_3_type_id_foreign');

            Schema::table('agreements', function (Blueprint $table) {
                $table->index('client_id', 'agreements_client_id_foreign');
                $table->index('service_id', 'agreements_service_id_foreign');
                $table->index('tariff_id', 'agreements_tariff_id_foreign');
            });

            Schema::table('bags', function (Blueprint $table) {
                $table->index('tracking_number');
            });

            Schema::table('checkpoint_code_event_code', function (Blueprint $table) {
                $table->index('checkpoint_code_id', 'checkpoint_code_event_code_checkpoint_code_id_foreign');
                $table->index('event_code_id', 'checkpoint_code_event_code_event_code_id_foreign');
            });

            Schema::table('delivery_route_service', function (Blueprint $table) {
                $table->index('delivery_route_id', 'delivery_route_service_delivery_route_id_foreign');
                $table->index('service_id', 'delivery_route_service_service_id_foreign');
            });

            Schema::table('delivery_routes', function (Blueprint $table) {
                $table->index('origin_location_id', 'delivery_routes_origin_location_id_foreign');
                $table->index('destination_location_id', 'delivery_routes_destination_location_id_foreign');

                $table->index(['controlled_transit_days', 'uncontrolled_transit_days', 'total_transit_days'], 'transit_days');
            });

            // Schema::table('dispatches', function (Blueprint $table) {
            //     $table->foreign('air_waybill_id')->references('id')->on('air_waybills')->onUpdate('cascade')->onDelete('cascade');
            // });

            Schema::table('events', function (Blueprint $table) {
                $table->index('package_id', 'events_package_id_foreign');
                $table->index('event_code_id', 'events_event_code_id_foreign');
                $table->index('last_checkpoint_id', 'events_last_checkpoint_id_foreign');
            });

            Schema::table('exchange_rates', function (Blueprint $table) {
                $table->index('currency_id', 'exchange_rates_currency_id_foreign');
            });

            Schema::table('invalid_checkpoints', function (Blueprint $table) {
                $table->index('package_id', 'invalid_checkpoints_package_id_foreign');
            });

            Schema::table('legs', function (Blueprint $table) {
                $table->index('delivery_route_id', 'legs_delivery_route_id_foreign');
                $table->index('provider_service_id', 'legs_provider_service_id_foreign');
            });

            Schema::table('locations', function (Blueprint $table) {
                $table->index('country_id', 'locations_country_id_foreign');
            });

            Schema::table('operation_performance_batches', function (Blueprint $table) {
                $table->foreign('panel_id')->references('id')->on('operation_panels')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('packages', function (Blueprint $table) {
                $table->index('agreement_id', 'packages_agreement_id_foreign');
                $table->index('delivery_route_id', 'packages_delivery_route_id_foreign');
                $table->index('leg_id', 'packages_leg_id_foreign');
            });

            Schema::table('services', function (Blueprint $table) {
                $table->index('billing_mode_id', 'services_billing_mode_id_foreign');
                $table->index('origin_location_id', 'services_origin_location_id_foreign');
                $table->index('destination_location_id', 'services_destination_location_id_foreign');
                $table->index('service_type_id', 'services_service_type_id_foreign');
            });

            Schema::table('tariff_templates', function (Blueprint $table) {
                $table->index('service_id', 'tariff_templates_service_id_foreign');
            });

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
