<?php

use App\Checkpoint;
use App\Package;
use App\Repositories\PackageRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFirstEventsMOPackages extends Migration
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

        $client_id = 14;

        $packages = $packageRepository->search([
            'client_id' => $client_id,
            'country_id' => 142
        ])->get();
        
        /**
         * @var Package $package
         */
        foreach ($packages as $package) {

            /**
             * @var Checkpoint $posted_at_origin
             */

            $firstCheckpoint = $package->getFirstCheckpoint();

            $date_to_mo1 = date('Y-m-d H:i:s', strtotime('-2 day', strtotime($firstCheckpoint->checkpoint_at)));
            $date_to_mo2 = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($firstCheckpoint->checkpoint_at)));

            $params = [
                'checkpoint_at' => $date_to_mo1,
                'checkpoint_code_id' => 4644,
                'timezone_id' => 39,
                'manual' => 0
            ];

            $c = $package->checkpoints()->create($params);

            $params = [
                'checkpoint_at' => $date_to_mo2,
                'checkpoint_code_id' => 4645,
                'timezone_id' => 39,
                'manual' => 0
            ];

            $c = $package->checkpoints()->create($params);

            // Update checkpoints
            $packageRepository->updateKeyCheckpoints($package);

            // Update package status
            $packageRepository->updateStatus($package);
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
