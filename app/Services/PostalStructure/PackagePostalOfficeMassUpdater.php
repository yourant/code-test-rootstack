<?php

namespace App\Services\PostalStructure;

use App\Models\Package;
use App\Repositories\PackageRepository;
use App\Repositories\PostalOfficeRepository;
use App\Repositories\ZipCodeRepository;
use App\Services\PostalStructure\ClassificationService;
use App\Models\ZipCode;
use DB;
use Exception;
use Illuminate\Support\Collection;

class PackagePostalOfficeMassUpdater
{

    /**
     * @var ZipCodeRepository
     */
    protected $zipCodeRepository;

    /**
     * @var PackageRepository
     */
    protected $packageRepository;

    /**
     * @var PostalOfficeRepository
     */
    protected $postalOfficeRepository;

    /** @var ClassificationService */
    protected $classificationService;

    public function __construct(
        ZipCodeRepository $zipCodeRepository,
        PostalOfficeRepository $postalOfficeRepository,
        PackageRepository $packageRepository,
        ClassificationService $classificationService
    ) {
        $this->zipCodeRepository = $zipCodeRepository;
        $this->postalOfficeRepository = $postalOfficeRepository;
        $this->packageRepository = $packageRepository;
        $this->classificationService = $classificationService;
    }

    public function detectZipCode(Package $package)
    {
//        $country_code = $package->getAgreementCountryCode();
        $country_code = $package->getAgreementServiceDestinationLocationCountryCode();

        /** @var ZipCode $zipCode */
        $zipCode = null;

        if ($country_code == 'MX') {
            // Mexico
            $zip = str_pad($package->zip, 5, '0', STR_PAD_LEFT);
            $zipCode = $this->zipCodeRepository->getByCodeAndCountryCode($zip, $country_code);
        } elseif ($country_code == 'CO') {
            // Colombia
            try {
                $zipCodes = $this->classificationService->guessZipCodesByFields($country_code, $package->state, $package->city);

                $zipCode = $zipCodes->first();
            } catch (Exception $e) {
                return false;
            }
        } else {
            // Default
            $zipCode = $this->zipCodeRepository->getByCodeAndCountryCode($package->zip, $country_code);
        }

        if (!$zipCode) {
            return false;
        }

        try {
            // Update zip_code_id in package
            DB::beginTransaction();

            if ($zipCode) {
                $this->packageRepository->setZipCode($package, $zipCode);
            } else {
                $this->packageRepository->removeZipCode($package);
            }

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            return false;
        }
    }

    public function detectPostalOffice(Package $package)
    {
        $zip = $package->zip;

//        $country_code = $package->getAgreementCountryCode();
        $country_code = $package->getAgreementServiceDestinationLocationCountryCode();
        if ($country_code == 'MX') {
            $zip = str_pad($zip, 5, '0', STR_PAD_LEFT);
        }

        if (!$zipCode = $this->zipCodeRepository->getByCodeAndCountryCode($zip, $country_code)) {
            logger("Zip code {$zip} missing in {$country_code}.");

            return false;
        }

        $postalOffice = $this->postalOfficeRepository->search(['zip_code_id' => $zipCode->id])->first();

        try {
            DB::beginTransaction();
            if ($postalOffice) {
                $this->packageRepository->setPostalOffice($package, $postalOffice);
            } else {
                $this->packageRepository->removePostalOffice($package);
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            return false;
        }
    }
}