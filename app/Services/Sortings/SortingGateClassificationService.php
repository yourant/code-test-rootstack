<?php

namespace App\Services\Sortings;

use App\Models\Sorting;
use App\Models\SortingGate;
use App\Repositories\SortingGateRepository;
use App\Services\PostalStructure\ClassificationService;
use Cache;

class SortingGateClassificationService
{

    /** @var \App\Repositories\SortingGateRepository */
    protected $sortingGateRepository;
    private $errors = [];
    /**
     * @var ClassificationService
     */
    private $classificationService;

    /**
     * SortingGateClassificationService constructor.
     * @param SortingGateRepository $sortingGateRepository
     * @param ClassificationService $classificationService
     */
    public function __construct(SortingGateRepository $sortingGateRepository, ClassificationService $classificationService)
    {
        $this->sortingGateRepository = $sortingGateRepository;
        $this->classificationService = $classificationService;
    }

    /**
     * @param Sorting $sorting
     * @param array $inputs
     * @return array
     */
    public function getGate(Sorting $sorting, array $inputs)
    {
        $gate = null;

        if ($this->haveSortingType($sorting, 'By Criteria')) {
            if (!isset($inputs['criteria_code'])) {
                $this->errors[] = 'No parameters were received for the type of criteria sorting';
            } else {
                $gate = $this->getGateByCriteria($sorting, $inputs['criteria_code']);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by criteria';
                }
            }
        }

        if ($this->haveSortingType($sorting, 'By Value')) {
            if (!isset($inputs['value'])) {
                $this->errors[] = 'No parameters were received for the type of value sorting';
            } else {
                $gate = $this->getGateByValue($sorting, $inputs['value']);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by value';
                }
            }
        }

        if ($this->haveSortingType($sorting, 'By Weight')) {
            if (!isset($inputs['weight'])) {
                $this->errors[] = 'No parameters were received for the type of weight sorting';
            } else {
                $gate = $this->getGateByWeight($sorting, $inputs['weight']);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by weight';
                }
            }
        }

        if ($this->haveSortingType($sorting, 'By Postal Office')) {
            if (!isset($inputs['zip_code']) && !isset($inputs['address']) && !isset($inputs['location']) && !isset($inputs['town']) && !isset($inputs['state']) && !isset($inputs['district'])) {
                $this->errors[] = 'No parameters were received for the type of postal office sorting';
            } else {
                $gate = $this->getGateByGeographical($sorting, $inputs);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by post office';
                }
            }
        }

        if ($this->haveSortingType($sorting, 'By Town')) {
            if (!isset($inputs['zip_code']) && !isset($inputs['address']) && !isset($inputs['location']) && !isset($inputs['town']) && !isset($inputs['state']) && !isset($inputs['district'])) {
                $this->errors[] = 'No parameters were received for the type of town sorting';
            } else {
                $gate = $this->getGateByGeographical($sorting, $inputs);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by town';
                }
            }
        }

        if ($this->haveSortingType($sorting, 'By State')) {
            if (!isset($inputs['zip_code']) && !isset($inputs['address']) && !isset($inputs['location']) && !isset($inputs['town']) && !isset($inputs['state']) && !isset($inputs['district'])) {
                $this->errors[] = 'No parameters were received for the type of state sorting';
            } else {
                $gate = $this->getGateByGeographical($sorting, $inputs);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by state';
                }
            }
        }

        if ($this->haveSortingType($sorting, 'By Region')) {
            if (!isset($inputs['zip_code']) && !isset($inputs['address']) && !isset($inputs['location']) && !isset($inputs['town']) && !isset($inputs['state']) && !isset($inputs['district'])) {
                $this->errors[] = 'No parameters were received for the type of region sorting';
            } else {
                $gate = $this->getGateByGeographical($sorting, $inputs);
                if (!$gate) {
                    $this->errors[] = 'No gates were found for the search by region';
                }
            }
        }

        return !$gate ? ['gate' => $sorting->getSortingGateDefault(), 'errors' => $this->errors] :
            ['gate' => $gate, 'errors' => null];
    }

    public function haveSortingType(Sorting $sorting, $type)
    {
        foreach ($sorting->sortingTypes as $sortingType) {
            if (is_array($type)) {
                if (in_array($sortingType->sortin_type_name, $type)) {
                    return true;
                }
            } else {
                if ($sortingType->sortin_type_name == $type) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param Sorting $sorting
     * @param string $value
     * @return array
     */
    public function getGateByValue(Sorting $sorting, $value)
    {
        return Cache::remember("sorting_{$sorting->id}_by_value_{$value}", 15, function () use ($sorting, $value) {
            return $this->sortingGateRepository->filter([
                'value'      => $value,
                'sorting_id' => $sorting->id
            ])->first();
        });
    }

    /**
     * @param Sorting $sorting
     * @param string $weight
     * @return array
     */
    public function getGateByWeight(Sorting $sorting, $weight)
    {
        return Cache::remember("sorting_{$sorting->id}_by_weight_{$weight}", 15, function () use ($sorting, $weight) {
            return $this->sortingGateRepository->filter([
                'weight'     => $weight,
                'sorting_id' => $sorting->id
            ])->first();
        });
    }

    /**
     * @param Sorting $sorting
     * @param string $criteria_code
     * @return array
     */
    public function getGateByCriteria(Sorting $sorting, $criteria_code)
    {
        return Cache::remember("sorting_{$sorting->id}_by_criteria_{$criteria_code}", 15, function () use ($sorting, $criteria_code) {
            return $this->sortingGateRepository->filter([
                'criteria_code' => $criteria_code,
                'sorting_id'    => $sorting->id
            ])->first();
        });
    }

    /**
     * @param SortingGate $sortingGate
     * @return string
     */
    public function getCodeForPackage(SortingGate $sortingGate)
    {
        $code = 'NA';
        $countryCode = null;
        $service = $sortingGate->getSortingServiceCode();
        if ($this->haveSortingType($sortingGate->sorting, ['By Town', 'By State', 'By Region', 'By Postal Office'])) {
            $countryCode = $sortingGate->getSortingGateCriteriaZipCodeCountryCode();
        }
        $service = $this->transformStrings($service, 11);
        $gate_code = $this->transformStrings($sortingGate->gate_code, 8);
        $gate_number = $this->transformStrings($sortingGate->gate_number, 2);
        if (!$countryCode) {
            $countryCode = $sortingGate->getSortingServiceCountryCode();
        }
        return strtoupper($code . $countryCode . $service . $gate_code . $gate_number);
    }

    public function transformStrings($string, $length)
    {
        $string = strip_accents($string);
        if (strlen($string) > $length) {
            return substr($string, 0, $length);
        } else {
            if (strlen($string) == $length) {
                return $string;
            } else {
                $faltante = '';
                for ($i = 0; $i < ($length - strlen($string)); $i++) {
                    $faltante .= '0';
                }
                return $faltante . $string;
            }
        }
    }

    public function getGateByGeographical(Sorting $sorting, array $inputs)
    {
        if (isset($inputs['zip_code'])) {
            $gate = Cache::remember("sorting_{$sorting->id}_by_zip_code_{$inputs['zip_code']}", 15, function () use ($sorting, $inputs) {
                return $this->sortingGateRepository->filter([
                    'zip_code'   => $inputs['zip_code'],
                    'sorting_id' => $sorting->id
                ])->first();
            });
            if ($gate) {
                return $gate;
            }
        }

        $district = (isset($inputs['district'])) ? $inputs['district'] : null;
        $location = (isset($inputs['location'])) ? $inputs['location'] : null;
        $state = (isset($inputs['state'])) ? $inputs['state'] : null;
        $town = (isset($inputs['town'])) ? $inputs['town'] : null;
        $zip_code = (isset($inputs['zip_code'])) ? $inputs['zip_code'] : null;

        // Guess Zip Codes
        $zipCodes = $this->classificationService->guessZipCodesByFields($sorting->getServiceCountryCode(), $state, $town, $district, null, null, null, $zip_code, $location);

        // Search Gate
        $gate = $this->sortingGateRepository->filter([
            'zip_code'   => $zipCodes->pluck('code')->toArray(),
            'sorting_id' => $sorting->id
        ])->first();

        return $gate;
    }
}


