<?php

use App\Agreement;
use App\Bag;
use App\Client;
use App\Dispatch;
use App\Package;
use App\Repositories\AgreementRepository;
use App\Repositories\BagRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DispatchRepository;
use App\Repositories\PackageRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MovePackagesBesskyToMailun extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var DispatchRepository $dispatchRepository */
        $dispatchRepository = app(DispatchRepository::class);

        /** @var BagRepository $bagRepository */
        $bagRepository = app(BagRepository::class);

        /** @var ClientRepository $clientRepository */
        $clientRepository = app(ClientRepository::class);

        /** @var PackageRepository $packageRepository */
        $packageRepository = app(PackageRepository::class);

        /** @var AgreementRepository $agreementRepository */
        $agreementRepository = app(AgreementRepository::class);

        $objPHPExcel = PHPExcel_IOFactory::load(base_path("/database/2017_02_02_115851_move_packages_bessky_to_mailun.xlsx"));
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $sheet->getHighestRow();

        try{

            DB::beginTransaction();

            for ($row = 2; $row <= $highestRow; ++$row) {

                $tn = trim($sheet->getCellByColumnAndRow(0, $row)->getValue());

                /** @var Package $package */
                $package = $packageRepository->search(['tracking_number' => $tn])->with(['bag.dispatch.airWaybill'])->first();
                if(!$package){
                    throw new Exception("Package not found: " . $tn);
                }

                $subaccount_name = trim($sheet->getCellByColumnAndRow(1, $row)->getValue());
                /** @var Client $subaccount */
                $subaccount = $clientRepository->getByName($subaccount_name);
                if(!$subaccount){
                    throw new Exception("Client not found: " . $subaccount_name);
                }

                /** @var Bag $original_bag */
                $original_bag = $package->bag;

                /** @var Dispatch $original_dispatch */
                $original_dispatch = $original_bag->dispatch;

                /** @var Agreement $agreement */
                $original_agreement = $original_dispatch->agreement;

                $new_agreement = $agreementRepository->search([
                    'client_id' => $subaccount->id,
                    'country_id' => $original_agreement->country_id
                ])->first();

                if(!$new_agreement){
                    throw new Exception("Agreement not found: " . $subaccount_name . ". Country: " . $original_agreement->getCountryName());
                }

                // Create dispatch
                $new_dispatch = $dispatchRepository->firstOrCreate([
                    'agreement_id'   => $new_agreement->id,
                    'air_waybill_id' => $original_dispatch->getAirWaybillId(),
                    'number'         => $original_dispatch->number,
                    'year'           => $original_dispatch->year,
                ]);

                $new_bag = $bagRepository->firstOrCreate([
                    'dispatch_id'     => $new_dispatch->id,
                    'tracking_number' => $original_bag->tracking_number
                ]);

                $packageRepository->setBag($package, $new_bag);

                logger("Package {$package->tracking_number} moved to {$subaccount_name}");
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            logger($e->getMessage());
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
