<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 12/1/2018
 * Time: 4:34 PM
 */

namespace App\Repositories;

use App\Models\AdminLevel1;
use App\Models\Country;
use App\Services\Fuzzy\FuzzySearchService;

class AdminLevel1Repository extends AbstractRepository
{
    /** @var FuzzySearchService */
    protected $fuzzySearchService;

    function __construct(AdminLevel1 $model, FuzzySearchService $fuzzySearchService)
    {
        $this->model = $model;
        $this->fuzzySearchService = $fuzzySearchService;
    }

    public function getByName($name)
    {
        return $this->model->whereName($name)->first();
    }

    public function getByNameAndCountryId($name, $country_id)
    {
        return $this->search(compact('name', 'country_id'))->first();
    }

    public function addAdminLevel2(AdminLevel1 $adminLevel1, array $adminLevel2 = [])
    {
        return $adminLevel1->adminLevels2()->create($adminLevel2);
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function search(array $params = [])
    {
        $query = $this->model
            ->distinct()
            ->select('admin_level_1.*');

        if (isset($params['name']) && $params['name']) {
            $query = $query->ofName($params['name']);
        }

        if (isset($params['country_id']) && $params['country_id']) {
            $query = $query->ofCountryId($params['country_id']);
        }

        if (isset($params['sorting_gate_criteria_id']) && $params['sorting_gate_criteria_id']) {
            $query->join('admin_level_2', 'admin_level_1.id', '=', 'admin_level_2.admin_level_1_id')
                ->join('admin_level_3', 'admin_level_2.id', '=', 'admin_level_3.admin_level_2_id')
                ->join('zip_codes', 'admin_level_3.id', '=', 'zip_codes.admin_level_3_id')
                ->join('sorting_gate_criteria_zip_code', 'zip_codes.id', '=', 'sorting_gate_criteria_zip_code.zip_code_id');
            if(is_array($params['sorting_gate_criteria_id'])){
                $query->whereIn('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $params['sorting_gate_criteria_id']);
            } else {
                $query->where('sorting_gate_criteria_zip_code.sorting_gate_criteria_id', $params['sorting_gate_criteria_id']);
            }
        }

        return $query->orderBy('admin_level_1.name', 'asc');
    }

    public function getRegions()
    {
        $query = $this->model
            ->distinct()
            ->join('countries', 'admin_level_1.country_id', '=', 'countries.id')
            ->select('admin_level_1.region')
            ->orderBy('admin_level_1.region', 'asc');

        return $query->get();
    }

    public function fuzzySearchByName($name, Country $country)
    {
        $states = $this->search(['country_id' => $country->id])->get();

        return $this->fuzzySearchService->search($states, $name);
    }
}