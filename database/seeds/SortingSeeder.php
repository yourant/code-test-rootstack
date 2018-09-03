<?php

use App\Models\Country;
use App\Models\PostalOffice;
use App\Models\Service;
use App\Models\Sorting;
use App\Models\SortingGateCriteria;
use App\Models\SortingType;
use App\Repositories\AdminLevel3Repository;
use App\Repositories\CountryRepository;
use App\Repositories\PostalOfficeRepository;
use App\Repositories\ProviderRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SortingGateCriteriaRepository;
use App\Repositories\SortingGateRepository;
use App\Repositories\SortingRepository;
use App\Repositories\SortingTypeRepository;
use App\Repositories\ZipCodeRepository;
use App\Services\Sortings\SortingCreationService;
use App\Services\Sortings\SortingGateCreationService;
use Illuminate\Database\Seeder;

class SortingSeeder extends Seeder
{
    /** @var CountryRepository */
    protected $countryRepository;

    /** @var ServiceRepository */
    protected $serviceRepository;

    /** @var SortingRepository */
    protected $sortingRepository;

    /** @var SortingGateRepository */
    protected $sortingGateRepository;

    /** @var SortingTypeRepository */
    protected $sortingTypeRepository;

    /** @var SortingGateCriteriaRepository */
    protected $sortingGateCriteriaRepository;

    /** @var SortingCreationService */
    protected $sortingCreationService;

    /** @var SortingGateCreationService */
    protected $sortingGateCreationService;

    /** @var ZipCodeRepository */
    protected $zipCodeRepository;

    /** @var AdminLevel3Repository */
    protected $adminLevel3Repository;

    /** @var PostalOfficeRepository */
    protected $postalOfficeRepository;

    /** @var ProviderRepository */
    protected $providerRepository;

    public function __construct(
        CountryRepository $countryRepository,
        ServiceRepository $serviceRepository,
        SortingRepository $sortingRepository,
        SortingGateRepository $sortingGateRepository,
        SortingTypeRepository $sortingTypeRepository,
        SortingGateCriteriaRepository $sortingGateCriteriaRepository,
        SortingCreationService $sortingCreationService,
        SortingGateCreationService $sortingGateCreationService,
        ZipCodeRepository $zipCodeRepository,
        AdminLevel3Repository $adminLevel3Repository,
        PostalOfficeRepository $postalOfficeRepository,
        ProviderRepository $providerRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->serviceRepository = $serviceRepository;
        $this->sortingRepository = $sortingRepository;
        $this->sortingGateRepository = $sortingGateRepository;
        $this->sortingTypeRepository = $sortingTypeRepository;
        $this->sortingGateCriteriaRepository = $sortingGateCriteriaRepository;
        $this->sortingCreationService = $sortingCreationService;
        $this->sortingGateCreationService = $sortingGateCreationService;
        $this->zipCodeRepository = $zipCodeRepository;
        $this->adminLevel3Repository = $adminLevel3Repository;
        $this->postalOfficeRepository = $postalOfficeRepository;
        $this->providerRepository = $providerRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Countries
        /** @var Country $countryBrasil */
        $countryBrasil = $this->countryRepository->getByCode('BR');
        /** @var Country $countryChile */
        $countryChile = $this->countryRepository->getByCode('CL');
        /** @var Country $countryColombia */
        $countryColombia = $this->countryRepository->getByCode('CO');
        /** @var Country $countryMexico */
        $countryMexico = $this->countryRepository->getByCode('MX');
        /** @var Country $countryPeru */
        $countryPeru = $this->countryRepository->getByCode('PE');

        try {
            DB::beginTransaction();

            // Initialize Sorting Types
            /** @var SortingType $sortingTypeRegion */
            $sortingTypeRegion = $this->sortingTypeRepository->firstOrCreate(['name' => 'geographical_region', 'description' => 'Geographical - Region']);
            /** @var SortingType $sortingTypeState */
            $sortingTypeState = $this->sortingTypeRepository->firstOrCreate(['name' => 'geographical_state', 'description' => 'Geographical - State']);
            /** @var SortingType $sortingTypeTown */
            $sortingTypeTown = $this->sortingTypeRepository->firstOrCreate(['name' => 'geographical_town', 'description' => 'Geographical - Town']);
            /** @var SortingType $sortingTypePostalOffice */
            $sortingTypePostalOffice = $this->sortingTypeRepository->firstOrCreate(['name' => 'geographical_postal_office', 'description' => 'Geographical - Postal Office']);

            /** @var SortingType $sortingTypeWeight */
            $sortingTypeWeight = $this->sortingTypeRepository->firstOrCreate(['name' => 'particular_weight', 'description' => 'Particular - Weight']);

            /** @var SortingType $sortingTypeCriteria */
            $sortingTypeCriteria = $this->sortingTypeRepository->firstOrCreate(['name' => 'particular_criteria', 'description' => 'Particular - Criteria']);

            /** @var SortingType $sortingTypeByValue */
            $sortingTypeByValue = $this->sortingTypeRepository->firstOrCreate(['name' => 'by_value', 'description' => 'Value - Declared']);

            /**
             * SERVICE: Mexico - Express
             *
             * SORTING: Geographic by postal office
             */

            /** @var Service $serviceMexicoExpress */
            $serviceMexicoExpress = $this->serviceRepository->getByCode('CN0015MX');

            /** @var Sorting $sortingMexicoExpress */
            $sortingMexicoExpress = $this->sortingCreationService->create('Mexico Express', $serviceMexicoExpress, collect([$sortingTypePostalOffice])->pluck('id')->toArray());

            $defaultGate = $this->sortingGateRepository->getByNumber($sortingMexicoExpress->id, 1);

            // Update default gate
            $this->sortingGateRepository->update($defaultGate, [
                'name' => 'RESTO',
                'code' => 'NAMX000PRIORITY000RESTO01'
            ]);

            $dictionary = [
                'NAMX000PRIORITY0PANTACO02' => 'COM Pantaco Mensajería, CDMX.',
                'NAMX000PRIORITYNACIONAL03' => 'COM Nacional Benito Juárez, CDMX.',
                'NAMX000PRIORITY00PUEBLA04' => 'COM Puebla, Pue.',
                'NAMX000PRIORITY0TIZAPAN05' => 'COM Tizapán, CDMX.',
                'NAMX000PRIORITYGUADALAJ06' => 'COM Guadalajara, Jal.',
                'NAMX000PRIORITYSANNICOL07' => 'COM San Nicolás de los Garza, NL',
            ];

            foreach ($dictionary as $k => $v) {
                $name = substr($k, 15, 8);
                $number = intval(substr($k, 23, 2));
                $gate = $this->sortingGateRepository->create([
                    'sorting_id' => $sortingMexicoExpress->id,
                    'name'       => $name,
                    'number'     => $number,
                    'code'       => $k,
                    'default'    => false,
                ]);

                /** @var SortingGateCriteria $sortingGateCriteria */
                $sortingGateCriteria = $this->sortingGateCriteriaRepository->create([
                    'sorting_gate_id' => $gate->id,
                    'sorting_type_id' => $sortingTypePostalOffice->id
                ]);

                /** @var PostalOffice $postalOffice */
                $postalOffice = $this->postalOfficeRepository->search(['name' => $v])->first();

                $this->sortingGateCriteriaRepository->attachZipCodes($sortingGateCriteria, $postalOffice->zipCodes->pluck('id'));
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
