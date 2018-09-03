<?php

namespace App\Repositories;

use App\Models\Sorting;
use App\Models\SortingType;
use Illuminate\Support\Collection;

/**
 * Class SortingRepository
 * @package App\Repositories
 */
class SortingRepository extends AbstractRepository
{
    /**
     * SortingRepository constructor.
     * @param Sorting $model
     */
    function __construct(Sorting $model)
    {
        $this->model = $model;
    }

    public function getByServiceCode($service_code)
    {
        return $this->filter(compact('service_code'))->first();
    }

    /**
     * @param $name
     *
     * @return Model
     */
    function getByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function filter(array $filters = [])
    {
        $query = $this->model->select('sortings.*');

        $joins = collect();

        if (isset($filters['country_id'])) {
            $this->addJoin($joins, 'services', 'services.sorting_id', 'sortings.id');
            $this->addJoin($joins, 'locations as locations_origin', 'services.origin_location_id', 'locations_origin.id');
            $this->addJoin($joins, 'locations as locations_destination', 'services.destination_location_id', 'locations_destination.id');
            $this->addJoin($joins, 'countries as countries_origin', 'locations_origin.country_id', 'countries_origin.id');
            $this->addJoin($joins, 'countries as countries_destination', 'locations_destination.country_id', 'countries_destination.id');
            $query->where(function ($q) use ($filters) {
                $q->orWhereIn('countries_origin.id', $filters['country_id']);
                $q->orWhereIn('countries_destination.id', $filters['country_id']);
            });
        }

        if (isset($filters['service_id'])) {
            $this->addJoin($joins, 'services', 'services.sorting_id', 'sortings.id');
            $query->ofServiceId($filters['service_id']);
        }

        if (isset($filters['service_code']) && $filters['service_code']) {
            $this->addJoin($joins, 'services', 'services.sorting_id', 'sortings.id');
            $query->where('services.code', strtoupper($filters['service_code']));
        }

        if (isset($filters['sorting_type_id'])) {
            $this->addJoin($joins, 'sorting_sorting_type', 'sorting_sorting_type.sorting_id', 'sortings.id');
            $query->ofSortingTypeId($filters['sorting_type_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query;
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

    /**
     * @param Sorting $sorting
     * @param SortingType $sortingType
     * @param array $input
     * @return mixed
     */
    public function updateSortingType(Sorting $sorting, SortingType $sortingType, array $input)
    {
        return $sorting->sortingTypes()->updateExistingPivot($sortingType->id, $input);
    }

    /**
     * @param Sorting $sorting
     * @param SortingType $sortingType
     * @return bool
     */
    public function attachSortingType(Sorting $sorting, SortingType $sortingType)
    {
        $sorting->sortingTypes()->attach($sortingType->id);

        return $sorting->save();
    }

    /**
     * @param Sorting $sorting
     * @param $sortingTypeIds
     * @return mixed
     */
    public function syncSortingTypes(Sorting $sorting, $sortingTypeIds)
    {
        return $sorting->sortingTypes()->sync($sortingTypeIds);
    }
} 