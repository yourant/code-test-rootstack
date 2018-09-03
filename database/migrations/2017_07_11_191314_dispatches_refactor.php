<?php

use App\Dispatch;
use App\Repositories\BagRepository;
use App\Repositories\DispatchRepository;
use App\Repositories\PackageRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DispatchesRefactor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ini_set('memory_limit', '2048M');

        Schema::table('packages', function(Blueprint $table) {
            $table->dropForeign('packages_bag_id_foreign');
        });

        try {
            DB::beginTransaction();

            $dispatches = DB::select('SELECT * FROM dispatches');

            $dispatches_deleted = [];

            /** @var Dispatch $dispatch */
            foreach ($dispatches as $dispatch) {

                if (in_array($dispatch->id, $dispatches_deleted)) {
                    continue;
                }

                logger("Nuevo Despacho ID: {$dispatch->id} / AWB: {$dispatch->air_waybill_id} / Number: {$dispatch->number} / Year: {$dispatch->year}");

                if ($dispatch->air_waybill_id) {
                    $dispatch_ids_duplicated = DB::select(
                        "SELECT id FROM dispatches
                          where dispatches.year = {$dispatch->year}
                          and dispatches.number = {$dispatch->number}
                          and dispatches.air_waybill_id = {$dispatch->air_waybill_id}
                          and dispatches.id != {$dispatch->id}"
                    );
                } else {
                    $dispatch_ids_duplicated = DB::select(
                        "SELECT id FROM dispatches
                          where dispatches.year = {$dispatch->year}
                          and dispatches.number = {$dispatch->number}
                          and dispatches.air_waybill_id is null
                          and dispatches.id != {$dispatch->id}"
                    );
                }

                $dispatch_ids_duplicated = array_pluck($dispatch_ids_duplicated, 'id');

                if (!empty($dispatch_ids_duplicated)) {

                    logger("Se encontraron los siguientes despachos repetidos:");
                    logger($dispatch_ids_duplicated);
                    logger("Busco Sacas");

                    $dispatches_to_search = $dispatch_ids_duplicated;
                    $dispatches_to_search[] = $dispatch->id;
                    $dispatches_to_search_imploded = implode(',', $dispatches_to_search);

                    $bags = DB::select("SELECT * FROM bags where bags.dispatch_id in ({$dispatches_to_search_imploded})");

                    $bags_count = count($bags);
                    logger("El despacho contiene un total de {$bags_count} sacas");

                    $bags_deleted = [];

                    foreach ($bags as $bag) {

                        if (in_array($bag->id, $bags_deleted)) {
                            continue;
                        }

                        $bags_ids_duplicated = DB::select("
                              SELECT id FROM bags
                              where bags.dispatch_id in ({$dispatches_to_search_imploded})
                              and bags.tracking_number = '{$bag->tracking_number}'
                              and bags.id != {$bag->id}
                          ");

                        $bags_ids_duplicated = array_pluck($bags_ids_duplicated, 'id');

                        logger("Se encontraron los siguientes SACAS repetidas:");
                        logger($bags_ids_duplicated);

                        if (!empty($bags_ids_duplicated)) {

                            $bags_duplicated_imploded = implode(',', $bags_ids_duplicated);

                            logger($bags_duplicated_imploded);

                            logger("Moviendo paquetes de sacas a la Saca {$bag->tracking_number} / ID: {$bag->id}");

                            DB::update("
                                    UPDATE packages SET packages.bag_id = {$bag->id} WHERE packages.bag_id in ({$bags_duplicated_imploded})
                                ");

                            logger("Eliminando sacas duplicadas");

                            DB::delete("
                                    DELETE FROM bags WHERE bags.id in ({$bags_duplicated_imploded})
                                ");

                            $bags_deleted = array_merge($bags_deleted, $bags_ids_duplicated);
                        }

                        logger("Asignando saca resultante al despacho resultante");

                        DB::update("
                                    UPDATE bags SET bags.dispatch_id = {$dispatch->id} WHERE bags.id = {$bag->id}
                                ");
                    }

                    $dispatch_ids_duplicated_imploded = implode(',', $dispatch_ids_duplicated);

                    logger("Eliminando despachos duplicados");

                    DB::delete("
                          DELETE FROM dispatches WHERE dispatches.id in ({$dispatch_ids_duplicated_imploded})
                    ");

                    $dispatches_deleted = array_merge($dispatches_deleted, $dispatch_ids_duplicated);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            logger($e->getMessage());
            logger($e->getTraceAsString());
            DB::rollBack();
        }

        Schema::table('dispatches', function(Blueprint $table) {
//            $table->dropForeign('dispatches_agreement_id_foreign');

            $table->dropUnique('dispatches_agreement_id_number_year_air_waybill_id_unique');

            $table->dropColumn('agreement_id');

            $table->unique(['number', 'year', 'air_waybill_id']);
        });

        Schema::table('packages', function(Blueprint $table) {
            $table->foreign('bag_id')->references('id')->on('bags')->onUpdate('cascade')->onDelete('set null');
        });
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
