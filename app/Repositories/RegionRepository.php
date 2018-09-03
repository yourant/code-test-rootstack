<?php

namespace App\Repositories;

use App\Models\Region;

class RegionRepository extends AbstractRepository
{

    function __construct(Region $model)
    {
        $this->model = $model;
    }

    public function search(array $filters = [])
    {
        $query = $this->model
            ->select('regions.*')
            ->distinct();

        if (isset($filters['country_id']) && $filters['country_id']) {
            $query = $query->ofCountryId($filters['country_id']);
        }

        if (isset($filters['sorting_gate_criteria_id']) && $filters['sorting_gate_criteria_id']) {
            $query->join('admin_level_1', 'regions.id', '=', 'admin_level_1.region_id')
                ->join('admin_level_2', 'admin_level_1.id', '=', 'admin_level_2.admin_level_1_id')
                ->join('admin_level_3', 'admin_level_2.id', '=', 'admin_level_3.admin_level_2_id')
                ->join('zip_codes', 'admin_level_3.id', '=', 'zip_codes.admin_level_3_id')
                ->join('sorting_gate_criteria_zip_code', 'zip_codes.id', '=', 'sorting_gate_criteria_zip_code.zip_code_id');
            if(is_array($filters['sorting_gate_criteria_id'])){
                $query->whereIn('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $filters['sorting_gate_criteria_id']);
            } else {
                $query->where('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $filters['sorting_gate_criteria_id']);
            }
        }

        return $query;
    }
} 