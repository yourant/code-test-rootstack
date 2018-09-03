<?php

namespace App\Repositories;

use App\Models\SortingGateCriteria;
use App\Models\ZipCode;
use DB;

/**
 * Class SortingGateCriteriaRepository
 * @package App\Repositories
 */
class SortingGateCriteriaRepository extends AbstractRepository
{
    /**
     * SortingGateCriteriaRepository constructor.
     * @param SortingGateCriteria $model
     */
    function __construct(SortingGateCriteria $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function filter(array $filters = [])
    {
        $query = $this->model->select('sorting_gate_criterias.*');
        
        $joins = collect();

        if (isset($filters['country_id'])) {
            $this->addJoin($joins, 'services', 'services.sorting_id', 'sortings.id');
            $query->ofCountryId($filters['country_id']);
        }

        if (isset($filters['sorting_gate_id']) && $filters['sorting_gate_id']) {
            $this->addJoin($joins, 'sorting_gates', 'sorting_gates.id', 'sorting_gate_criterias.sorting_gate_id');
             $this->addJoin($joins, 'sorting_gate_criteria_zip_code', 'sorting_gate_criteria_zip_code.sorting_gate_criteria_id', 'sorting_gate_criterias.id');
            $query->where("sorting_gate_criterias.sorting_gate_id", $filters['sorting_gate_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
           $item = json_decode($item);
           $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query;
    }

    /**
     * @param SortingGateCriteria $sortingGateCriteria
     * @param ZipCode $zipCode
     * @return bool
     */
    public function attachZipCode(SortingGateCriteria $sortingGateCriteria, ZipCode $zipCode) {
        $sortingGateCriteria->zipCodes()->attach($zipCode->id);

        return $sortingGateCriteria->save();
    }

    /**
     * @param SortingGateCriteria $sortingGateCriteria
     * @param $zipCodeIds
     * @return bool
     */
    public function attachZipCodes(SortingGateCriteria $sortingGateCriteria, $zipCodeIds) {
        $sql = "INSERT INTO sorting_gate_criteria_zip_code (sorting_gate_criteria_id, zip_code_id) VALUES";
        foreach ($zipCodeIds as $zipCodeId){
            $sql.= "({$sortingGateCriteria->id}, {$zipCodeId}), ";
        }
        $sql = substr($sql, 0, -2);
        return DB::insert(DB::raw($sql));
    }

    /**
     * @param SortingGateCriteria $sortingGateCriteria
     * @param $zipCodeIds
     * @return bool
     */
    public function syncZipCode(SortingGateCriteria $sortingGateCriteria, $zipCodeIds) {
        return $sortingGateCriteria->zipCodes()->sync($zipCodeIds);
    }

    /**
     * @param SortingGateCriteria $sortingGateCriteria
     * @param ZipCode $zipCode
     * @return bool
     */
    public function detachZipCode(SortingGateCriteria $sortingGateCriteria, ZipCode $zipCode) {
        $sortingGateCriteria->zipCodes()->detach($zipCode->id);

        return $sortingGateCriteria->save();
    }


    /**
     * @param Collection $joins
     * @param $table
     * @param $first
     * @param $second
     * @param string $join_type
     */
    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

}