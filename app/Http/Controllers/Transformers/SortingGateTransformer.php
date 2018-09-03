<?php

namespace App\Http\Controllers\Transformers;

use App\Repositories\AdminLevel1Repository;
use App\Repositories\AdminLevel2Repository;
use App\Repositories\PostalOfficeRepository;
use App\Repositories\RegionRepository;

class SortingGateTransformer
{
    /** @var \App\Repositories\RegionRepository */
    protected $regionRepository;

    /** @var \App\Repositories\AdminLevel1Repository */
    protected $adminLevel1Repository;

    /** @var \App\Repositories\AdminLevel2Repository */
    protected $adminLevel2Repository;

    /** @var \App\Repositories\PostalOfficeRepository */
    protected $postalOfficeRepository;

    /**
     * SortingGateTransformer constructor.
     * @param RegionRepository $regionRepository
     * @param AdminLevel1Repository $adminLevel1Repository
     * @param AdminLevel2Repository $adminLevel2Repository
     * @param PostalOfficeRepository $postalOfficeRepository
     */
    public function __construct(
        RegionRepository $regionRepository,
        AdminLevel1Repository $adminLevel1Repository,
        AdminLevel2Repository $adminLevel2Repository,
        PostalOfficeRepository $postalOfficeRepository
    ) {
        $this->regionRepository = $regionRepository;
        $this->adminLevel1Repository = $adminLevel1Repository;
        $this->adminLevel2Repository = $adminLevel2Repository;
        $this->postalOfficeRepository = $postalOfficeRepository;
    }

    /**
     * @param $sortingGates
     * @return mixed
     */
    public function transform($sortingGates)
    {
        $sortingGates->transform(function ($sortingGate) {
            $sortingGate->sortingGateCriterias->transform(function ($sortingGateCriteria) {
                if ($sortingGateCriteria->isByRegion()) {
                    $sortingGateCriteria->list_regions_names = $this->regionRepository
                        ->search(['sorting_gate_criteria_id' => $sortingGateCriteria->id])->select('regions.name')->get();
                } elseif ($sortingGateCriteria->isByState()) {
                    $sortingGateCriteria->list_states_names = $this->adminLevel1Repository
                        ->search(['sorting_gate_criteria_id' => $sortingGateCriteria->id])->select('admin_level_1.name')->get();
                } elseif ($sortingGateCriteria->isByTown()) {
                    $sortingGateCriteria->list_towns_names = $this->adminLevel2Repository
                        ->search(['sorting_gate_criteria_id' => $sortingGateCriteria->id])->select('admin_level_2.name')->get();
                } elseif ($sortingGateCriteria->isByPostalOffice()) {
                    $sortingGateCriteria->list_postal_offices_names = $this->postalOfficeRepository
                        ->search(['sorting_gate_criteria_id' => $sortingGateCriteria->id])->get();
                }

                return $sortingGateCriteria;
            });

            return $sortingGate;
        });

        return $sortingGates;
    }
}