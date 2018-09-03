<?php

namespace App\Repositories;

use App\Models\AdminLevel1;
use App\Models\AdminLevel2;
use App\Models\Country;
use App\Services\Fuzzy\FuzzySearchService;

class AdminLevel2Repository extends AbstractRepository
{

    /** @var FuzzySearchService */
    protected $fuzzySearchService;

    function __construct(AdminLevel2 $model, FuzzySearchService $fuzzySearchService)
    {
        $this->model = $model;
        $this->fuzzySearchService = $fuzzySearchService;
    }

    public function getByNameAndAdminLevel1($name, AdminLevel1 $adminLevels1)
    {
        return $this->model->whereName($name)->where('admin_level_1_id', $adminLevels1->id)->first();
    }

    public function getByName($name)
    {
        return $this->model->whereName($name)->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model
            ->distinct()
            ->select('admin_level_2.*');

        if (isset($filters['country_id']) && $filters['country_id']) {
            $query = $query
                ->join('admin_level_1', 'admin_level_2.admin_level_1_id', '=', 'admin_level_1.id')
                ->where('admin_level_1.country_id', $filters['country_id']);
        }

        if (isset($filters['admin_level_1_id']) && $filters['admin_level_1_id']) {
            $query = $query->where('admin_level_2.admin_level_1_id', $filters['admin_level_1_id']);
        }

        if (isset($filters['sorting_gate_criteria_id']) && $filters['sorting_gate_criteria_id']) {
            $query->join('admin_level_3', 'admin_level_2.id', '=', 'admin_level_3.admin_level_2_id')
                ->join('zip_codes', 'admin_level_3.id', '=', 'zip_codes.admin_level_3_id')
                ->join('sorting_gate_criteria_zip_code', 'zip_codes.id', '=', 'sorting_gate_criteria_zip_code.zip_code_id');
            if(is_array($filters['sorting_gate_criteria_id'])){
                $query->whereIn('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $filters['sorting_gate_criteria_id']);
            } else {
                $query->where('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $filters['sorting_gate_criteria_id']);
            }
        }

        if (isset($filters['sort_by']) && $filters['sort_by']) {
            $column = $filters['sort_by'];
            $direction = 'asc';
            if (isset($filters['sort_direction']) && $filters['sort_direction']) {
                $direction = $filters['sort_direction'];
            }
            return $query->orderBy($column, $direction);
        } else {
            return $query->orderBy('admin_level_2.name', 'asc');
        }
    }

    public function fuzzySearchByNameAndAdminLevel1($name, AdminLevel1 $adminLevels1)
    {
        $towns = $this->search(['admin_level_1_id' => $adminLevels1->id])->get();

        return $this->fuzzySearchService->search($towns, $name);
    }

    public function fuzzySearchByNameAndCountry($name, Country $country)
    {
        $towns = $this->search(['country_id' => $country->id])->get();

        return $this->fuzzySearchService->search($towns, $name);
    }
}