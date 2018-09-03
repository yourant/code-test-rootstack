<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 12/1/2018
 * Time: 4:34 PM
 */

namespace App\Repositories;

use App\Models\AdminLevel1;
use App\Models\AdminLevel2;
use App\Models\AdminLevel3;
use App\Models\Country;
use App\Services\Fuzzy\FuzzySearchService;


class AdminLevel3Repository extends AbstractRepository
{
    /** @var FuzzySearchService */
    protected $fuzzySearchService;

    function __construct(AdminLevel3 $model, FuzzySearchService $fuzzySearchService)
    {
        $this->model = $model;
        $this->fuzzySearchService = $fuzzySearchService;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $table = $this->model->getTable();
        $query = $this->model
            ->select("{$table}.*")
            ->distinct('admin_level_3.id');

        if (isset($filters['name']) && $filters['name']) {
            $query = $query->ofName($filters['name']);
        }

        if (isset($filters['name_alt']) && $filters['name_alt']) {
            $query = $query->ofNameAlt($filters['name_alt']);
        }

        if (isset($filters['country_code']) && $filters['country_code']) {
            $query = $query->join('admin_level_2', 'admin_level_2.id', '=', 'admin_level_3.admin_level_2_id')
                ->join('admin_level_1', 'admin_level_1.id', '=', 'admin_level_2.admin_level_1_id')
                ->join('countries', 'countries.id', '=', 'admin_level_1.country_id')
                ->ofCountryCode($filters['country_code']);
        }

        if (isset($filters['territorial_code']) && $filters['territorial_code']) {
            $query = $query->ofTerritorialCode($filters['territorial_code']);
        }

        if (isset($filters['abbreviation_code']) && $filters['abbreviation_code']) {
            $query = $query->ofAbbreviationCode($filters['abbreviation_code']);
        }

        if (isset($filters['admin_level_2_id']) && $filters['admin_level_2_id']) {
            $query = $query->where('admin_level_3.admin_level_2_id', $filters['admin_level_2_id']);
        }

        if (isset($filters['admin_level_1_id']) && $filters['admin_level_1_id']) {
            $query = $query->join('admin_level_2', 'admin_level_3.admin_level_2_id', '=', 'admin_level_2.id')
                ->where('admin_level_2.admin_level_1_id', $filters['admin_level_1_id']);
        }

        if (isset($filters['country_id']) && $filters['country_id']) {
            $query = $query
                ->join('admin_level_2', 'admin_level_2.id', '=', 'admin_level_3.admin_level_2_id')
                ->join('admin_level_1', 'admin_level_1.id', '=', 'admin_level_2.admin_level_1_id')
                ->where('admin_level_1.country_id', $filters['country_id']);
        }

        return $query->orderBy("{$table}.name", 'asc');
    }

    public function getByAbbreviationCode($code)
    {
        return $this->search(['abbreviation_code' => $code])->first();
    }

    public function getByNameAndAdminLevel2($name, AdminLevel2 $adminLevels2)
    {
        return $this->model->ofName($name)->where('admin_level_3.admin_level_2_id', $adminLevels2->id)->first();
    }

    public function fuzzySearchByNameAndAdminLevel2($name, AdminLevel2 $adminLevel2)
    {
        $townships = $this->search(['admin_level_2_id' => $adminLevel2->id])->get();

        return $this->fuzzySearchService->search($townships, $name);
    }

    public function fuzzySearchByNameAndAdminLevel1($name, AdminLevel1 $adminLevel1)
    {
        $townships = $this->search(['admin_level_1_id' => $adminLevel1->id])->get();

        return $this->fuzzySearchService->search($townships, $name);
    }

    public function fuzzySearchByNameAndCountry($name, Country $country)
    {
        $townships = $this->search(['country_id' => $country->id])->get();

        return $this->fuzzySearchService->search($townships, $name, ['name', 'name_alt']);
    }
}