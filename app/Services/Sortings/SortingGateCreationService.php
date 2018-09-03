<?php

namespace App\Services\Sortings;

use App\Models\Service;
use App\Models\Sorting;
use App\Models\SortingGate;
use App\Models\SortingType;
use App\Repositories\AdminLevel1Repository;
use App\Repositories\AdminLevel2Repository;
use App\Repositories\AdminLevel3Repository;
use App\Repositories\PostalOfficeRepository;
use App\Repositories\RegionRepository;
use App\Repositories\SortingGateCriteriaRepository;
use App\Repositories\SortingGateRepository;
use App\Repositories\ZipCodeRepository;
use Illuminate\Support\Str;

/**
 * Class SortingGateCreationService
 * @package App\Services\Sortings
 */
class SortingGateCreationService
{
    /** @var SortingGateRepository */
    protected $sortingGateRepository;

    /** @var SortingGateCriteriaRepository */
    protected $sortingGateCriteriaRepository;

    /** @var RegionRepository */
    protected $regionRepository;

    /** @var PostalOfficeRepository */
    protected $postalOfficeRepository;

    /** @var AdminLevel1Repository */
    protected $adminLevel1Repository;

    /** @var AdminLevel3Repository */
    protected $adminLevel3Repository;

    /** @var AdminLevel2Repository */
    protected $adminLevel2Repository;

    /** @var ZipCodeRepository */
    private $zipCodeRepository;

    public function __construct(
        SortingGateRepository $sortingGateRepository,
        SortingGateCriteriaRepository $sortingGateCriteriaRepository,
        RegionRepository $regionRepository,
        PostalOfficeRepository $postalOfficeRepository,
        AdminLevel1Repository $adminLevel1Repository,
        AdminLevel2Repository $adminLevel2Repository,
        AdminLevel3Repository $adminLevel3Repository,
        ZipCodeRepository $zipCodeRepository
    ) {
        $this->sortingGateRepository = $sortingGateRepository;
        $this->sortingGateCriteriaRepository = $sortingGateCriteriaRepository;
        $this->regionRepository = $regionRepository;
        $this->postalOfficeRepository = $postalOfficeRepository;
        $this->adminLevel1Repository = $adminLevel1Repository;
        $this->adminLevel2Repository = $adminLevel2Repository;
        $this->adminLevel3Repository = $adminLevel3Repository;
        $this->zipCodeRepository = $zipCodeRepository;
    }

    /**
     * @param Sorting $sorting
     * @return SortingGate
     */
    public function createDefaultGate(Sorting $sorting)
    {
        $name = 'DEFAULT';
        $number = 1;

        /** @var SortingGate $defaultGate */
        $defaultGate = $this->sortingGateRepository->create([
            'sorting_id' => $sorting->id,
            'number'     => $number,
            'name'       => $name,
            'code'       => self::generateCode($sorting->service, $name, $number),
            'default'    => true,
        ]);

        return $defaultGate;
    }

    private static function generateCode(Service $service, $gate_name, $gate_number = 1)
    {
        $gate_name = strip_accents($gate_name);
        $gate_name = preg_replace('/[^\da-z]/i', '', $gate_name);

        $continent_code = $service->getDestinationLocationCountryContinentAbbreviation();
        $country_code = $service->getDestinationLocationCountryCode();
        $service_code = str_pad($service->code, 11, '0', STR_PAD_LEFT);;
        $gate_name = str_pad($gate_name, 8, '0', STR_PAD_LEFT);
        $gate_number = str_pad($gate_number, 2, '0', STR_PAD_LEFT);

        return Str::upper("{$continent_code}{$country_code}{$service_code}{$gate_name}{$gate_number}");
    }

    public function insert(Sorting $sorting, array $input = [])
    {
        $gate = $this->insertGate($sorting, $input);
        foreach ($sorting->sortingTypes as $sortingType) {
            if ($sortingType->isByValue()) {
                $this->saveGateValue($gate, $sortingType, $input);
            }

            if ($sortingType->isByRegion()) {
                $this->saveGateRegion($gate, $sortingType, $input);
            }

            if ($sortingType->isByState()) {
                $this->saveGateState($gate, $sortingType, $input);
            }

            if ($sortingType->isByTown()) {
                $this->saveGateTown($gate, $sortingType, $input);
            }

            if ($sortingType->isByWeight()) {
                $this->saveGateWeight($gate, $sortingType, $input);
            }

            if ($sortingType->isByCriteria()) {
                $this->saveGateCriteria($gate, $sortingType, $input);
            }

            if ($sortingType->isByPostalOffice()) {
                $this->saveGatePostalOffice($gate, $sortingType, $input);
            }
        }

        return $gate;
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGateRegion(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        if (isset($input['region_id']) && $input['region_id']) {
            $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType);
            $regions = $this->regionRepository->findMany($input['region_id']);
            foreach ($regions as $region) {
                $zipCodes = $this->zipCodeRepository->search(['region_id' => $region->id])->select('zip_codes.id')->get();
                foreach ($zipCodes->chunk(5000) as $zipCode) {
                    $this->sortingGateCriteriaRepository->attachZipCodes($sortingGateCriteria, $zipCode->pluck('id'));
                }
            }
        }
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGateState(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        if (isset($input['state_id']) && $input['state_id']) {
            $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType);
            $states = $this->adminLevel1Repository->findMany($input['state_id']);
            foreach ($states as $state) {
                $zipCodes = $this->zipCodeRepository->search(['admin_level_1_id' => $state->id])->select('zip_codes.id')->get();
                $this->sortingGateCriteriaRepository->attachZipCodes($sortingGateCriteria, $zipCodes->pluck('id'));
            }
        }
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGateTown(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        if (isset($input['town_id']) && $input['town_id']) {
            $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType);
            $cities = $this->adminLevel2Repository->findMany($input['town_id']);
            foreach ($cities as $city) {
                $zipCodes = $this->zipCodeRepository->search(['admin_level_2_id' => $city->id])->select('zip_codes.id')->get();
                $this->sortingGateCriteriaRepository->attachZipCodes($sortingGateCriteria, $zipCodes->pluck('id'));
            }
        }
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGatePostalOffice(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        if (isset($input['postal_office_id']) && $input['postal_office_id']) {
            $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType);
            $postalOffices = $this->postalOfficeRepository->findMany($input['postal_office_id']);
            foreach ($postalOffices as $postalOffice) {
                $this->sortingGateCriteriaRepository->attachZipCodes($sortingGateCriteria, $postalOffice->zipCodes->pluck('id'));
            }
        }
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGateValue(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType, [
            'after_than'  => (isset($input['after_value']) && $input['after_value']) ? $input['after_value'] : null,
            'before_than' => (isset($input['before_value']) && $input['before_value']) ? $input['before_value'] : null
        ]);
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGateCriteria(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        if (isset($input['criteria']) && $input['criteria']) {
            $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType, [
                'criteria_code' => $input['criteria'],
            ]);
        }
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $input
     */
    public function saveGateWeight(SortingGate $gate, SortingType $sortingType, array $input = [])
    {
        $sortingGateCriteria = $this->insertGateCriteria($gate, $sortingType, [
            'after_than'  => (isset($input['after_weight']) && $input['after_weight']) ? $input['after_weight'] : null,
            'before_than' => (isset($input['before_weight']) && $input['before_weight']) ? $input['before_weight'] : null
        ]);
    }

    /**
     * @param Sorting $sorting
     * @return int
     */
    public function getGateNumber(Sorting $sorting)
    {
        $gates = $this->sortingGateRepository->filter([
            'sorting_id' => $sorting->id
        ])->orderBy('sorting_gates.gate_number', 'desc')->first();
        $number = $gates ? $gates->gate_number + 1 : 1;

        return $number;
    }

    /**
     * @param Sorting $sorting
     * @param array $input
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function insertGate(Sorting $sorting, array $input = [])
    {
        $number = $this->getGateNumber($sorting);
        $gate = $this->sortingGateRepository->create([
            'sorting_id'  => $sorting->id,
            'gate_number' => $number,
            'gate_code'   => $input['code']
        ]);

        return $gate;
    }

    /**
     * @param SortingGate $gate
     * @param SortingType $sortingType
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function insertGateCriteria(SortingGate $gate, SortingType $sortingType, array $data = [])
    {
        $resultant = array_merge($data, ['sorting_gate_id' => $gate->id, 'sorting_type_id' => $sortingType->id]);

        return $this->sortingGateCriteriaRepository->create($resultant);
    }
}
