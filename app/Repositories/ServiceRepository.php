<?php

namespace App\Repositories;

use App\Models\Service;
use App\Models\Sorting;
use Illuminate\Support\Collection;

class ServiceRepository extends AbstractRepository
{
    function __construct(Service $model)
    {
        $this->model = $model;
    }

    public function getByCode($code)
    {
        return $this->model->ofCode($code)->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function filter(array $filters = [])
    {
        $query = $this->model->select('services.*');

        if (isset($filters['sorting_id'])) {
            $query = $query->ofSortingId($filters['sorting_id']);
        }

        return $query;
    }

    public function search(array $filters = [])
    {
        $joins = collect();

        $query = $this->model
            ->distinct()
            ->select('services.*');

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['transit_days']) && $filters['transit_days']) {
            $query->ofTransitDays($filters['transit_days']);
        }

        if (isset($filters['origin_location_id']) && $filters['origin_location_id']) {
            $this->addJoin($joins, 'locations as origin_location', 'origin_location.id', 'services.origin_location_id');
            $query->ofOriginLocationId($filters['origin_location_id']);
        }

        if (isset($filters['destination_location_id']) && $filters['destination_location_id']) {
            $this->addJoin($joins, 'locations as destination_location', 'destination_location.id', 'services.destination_location_id');
            $query->ofDestinationLocationId($filters['destination_location_id']);
        }

        if (isset($filters['client_id']) && $filters['client_id']) {
            $this->addJoin($joins, 'agreements', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'clients', 'clients.id', 'agreements.client_id');
            $query->where('agreements.enabled', '=' ,true);
            $query->where('services.enabled', '=', true);
            $query->ofClientId($filters['client_id']);
        }

        if (isset($filters['exclude_service_id']) && $filters['exclude_service_id']) {
            $query->ofExcludeServiceId($filters['exclude_service_id']);
        }

        if (isset($filters['with_origin_countries']) && $filters['with_origin_countries']) {
            $this->addJoin($joins,'locations as origin_location', 'origin_location.id', 'services.origin_location_id');
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('code');
    }

    public function syncDeliveryRoutes(Service $service, Collection $delivery_routes)
    {
        return $service->deliveryRoutes()->sync($delivery_routes->toArray());
    }

    public function getDeliveryRoutes(Service $service)
    {
        return $service->deliveryRoutes;
    }

    /**
     * @param Service $service
     * @return bool
     */
    public function unassignSorting(Service $service)
    {
        $service->sorting()->dissociate();
        return $service->save();
    }

    /**
     * @param Service $service
     * @param Sorting $sorting
     * @return bool
     */
    public function assignSorting(Service $service, Sorting $sorting)
    {
        $service->sorting()->associate($sorting);
        return $service->save();
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}