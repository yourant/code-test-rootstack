<?php

use App\Models\DeliveryRoute;
use App\Repositories\DeliveryRouteRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdmissionsRefactorCleanup extends Migration
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

            logger("Start cleanup migration");

            // Delete old columns and tables
            // ------------------------------------------------------------------------------------------------

            logger("Starting to delete old columns and tables");

            Schema::table('provider_services', function (Blueprint $table) {
//                $table->dropForeign('service_types_last_checkpoint_code_id_foreign');
//                $table->dropColumn('last_checkpoint_code_id');
                $table->dropColumn('type');
                $table->dropColumn('service');
                $table->dropColumn('details');
                $table->dropColumn('default');
            });

            Schema::table('legs', function (Blueprint $table) {
                $table->dropForeign('legs_provider_service_id_foreign');
            });

            Schema::table('agreements', function (Blueprint $table) {
                $table->dropForeign('agreements_client_id_foreign');
                $table->dropForeign('agreements_country_id_foreign');
                $table->dropIndex('agreements_type_transit_days_controlled_transit_days_index');
            });

            Schema::table('packages', function (Blueprint $table) {
                $table->dropForeign('packages_agreement_id_foreign');
                $table->dropForeign('packages_leg_id_foreign');
                $table->dropColumn('agreement_id');
                $table->dropColumn('leg_id');
            });

            Schema::drop('legs');

            Schema::drop('agreements');

            Schema::rename('new_agreements', 'agreements');

            Schema::table('agreements', function (Blueprint $table) {
                $table->dropForeign('new_agreements_service_id_foreign');
                $table->dropForeign('new_agreements_client_id_foreign');
                $table->dropForeign('new_agreements_tariff_id_foreign');

                $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('tariff_id')->references('id')->on('tariffs')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::rename('new_legs', 'legs');

            Schema::table('packages', function (Blueprint $table) {
                $table->dropForeign('packages_new_agreement_id_foreign');
                $table->renameColumn('new_agreement_id', 'agreement_id');
                $table->foreign('agreement_id')->references('id')->on('agreements')->onUpdate('cascade')->onDelete('cascade');

                $table->dropForeign('packages_new_leg_id_foreign');
                $table->renameColumn('new_leg_id', 'leg_id');
                $table->foreign('leg_id')->references('id')->on('legs')->onUpdate('cascade')->onDelete('cascade');
            });

            DB::statement('ALTER INDEX IF EXISTS new_agreements_pkey RENAME TO agreements_pkey');
            DB::statement('ALTER INDEX IF EXISTS new_legs_pkey RENAME TO legs_pkey');

            Schema::drop('alert_details');
            Schema::drop('alerts');
            Schema::drop('boundaries');
            Schema::drop('checkpoint_code_milestone');
            Schema::drop('milestone_segment');
            Schema::drop('milestones');
            Schema::drop('segments');

            // Add constraints
            // ------------------------------------------------------------------------------------------------

            logger("Starting to add constraints");

            Schema::table('legs', function (Blueprint $table) {
                $table->unique(['delivery_route_id', 'provider_service_id', 'position', 'controlled'], 'legs_unique');
            });

            Schema::table('packages', function (Blueprint $table) {
                $table->dropForeign('packages_delivery_route_id_foreign');
            });

            Schema::table('packages', function (Blueprint $table) {
                $table->integer('delivery_route_id')->unsigned()->change();

                $table->foreign('delivery_route_id')->references('id')->on('delivery_routes')->onUpdate('cascade')->onDelete('cascade');
            });

            Schema::table('delivery_route_service', function (Blueprint $table) {
                $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('delivery_route_id')->references('id')->on('delivery_routes')->onUpdate('cascade')->onDelete('cascade');
            });

            // Calculate delivery routes transit days
            // ------------------------------------------------------------------------------------------------

            logger("Calculate delivery routes transit days");

            // RECALCULAR DIAS DE TRANSITO CALCULADOS DE LAS RUTAS
            /** @var DeliveryRouteRepository $deliveryRouteRepository */
            $deliveryRouteRepository = app(DeliveryRouteRepository::class);

            $deliveryRoutes = $deliveryRouteRepository->all();

            /** @var DeliveryRoute $deliveryRoute */
            foreach ($deliveryRoutes as $deliveryRoute) {
                $deliveryRouteRepository->update($deliveryRoute, [
                    'controlled_transit_days'   => $deliveryRoute->calculateControlledTransitDays(),
                    'uncontrolled_transit_days' => $deliveryRoute->calculateUncontrolledTransitDays(),
                    'total_transit_days'        => $deliveryRoute->calculateTotalTransitDays(),
                ]);
            }

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
