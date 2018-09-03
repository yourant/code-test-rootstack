<?php

use App\Checkpoint;
use App\Package;
use App\Repositories\PackageRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventsFirstLeg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @var PackageRepository $packageRepository
         */
        $packageRepository = app(PackageRepository::class);

        $clients_id = array(22, 27, 30, 31, 32, 33, 34, 35, 36, 37, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62);

        foreach ($clients_id as $client_id){
            
            $packages = $packageRepository->search([
                'client_id' => $client_id
            ])->get();

            //logger("Client ID: " . $client_id);
            //logger("Packages: " . $packages->count());

            /**
             * @var Package $package
             */
            foreach ($packages as $package){

                //logger("Package ID: " . $package->id);
                //logger("Package Tracking number: " . $package->tracking_number);

                /**
                 * @var Checkpoint $posted_at_origin
                 */
                $posted_at_origin = $package->checkpoints->filter(function ($c){
                    return $c->checkpoint_code_id == 4381;
                })->first();

                if(!$posted_at_origin){
                    //logger("Posted at Origin no encontrado.");
                    continue;
                }

                //logger("Posted at Origin: " . $posted_at_origin->checkpoint_at);

                $order_processed = $package->checkpoints->filter(function ($c){
                    return $c->checkpoint_code_id == 4721;
                })->first();

                $received = $package->checkpoints->filter(function ($c){
                    return $c->checkpoint_code_id == 4722;
                })->first();

                if(!$order_processed){
                    $date_to_order_processed = date('Y-m-d H:i:s', strtotime('-2 day', strtotime($posted_at_origin->checkpoint_at)));

                    //logger("Order processed: " . $date_to_order_processed);

                    $params = [
                        'checkpoint_at' => $date_to_order_processed,
                        'checkpoint_code_id' => 4721,
                        'timezone_id' => 110,
                        'manual' => 0
                    ];

                    $c = $package->checkpoints()->create($params);
                }

                if(!$received){
                    $date_to_received_at_warehouse = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($posted_at_origin->checkpoint_at)));

                    //logger("Received: " . $date_to_received_at_warehouse);

                    $params = [
                        'checkpoint_at' => $date_to_received_at_warehouse,
                        'checkpoint_code_id' => 4722,
                        'timezone_id' => 110,
                        'manual' => 0
                    ];

                    $c = $package->checkpoints()->create($params);
                }

                if(!$order_processed or !$received){
                    // Update checkpoints
                    $packageRepository->updateKeyCheckpoints($package);

                    // Update package status
                    $packageRepository->updateStatus($package);
                }
            }
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
