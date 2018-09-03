<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 12/1/2018
 * Time: 4:32 PM
 */

namespace App\Repositories;


use App\Models\Location;
use Illuminate\Support\Collection;

class LocationRepository extends AbstractRepository
{
    function __construct(Location $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search($filters = [])
    {
        $query = $this->model
            ->select('locations.*')
            ->distinct('locations.id');

        $joins = collect();

        if (isset($filters['origin_with_delivery_routes']) && $filters['origin_with_delivery_routes']) {
            $this->addJoin($joins, 'delivery_routes as origin_delivery_routes', 'origin_delivery_routes.origin_location_id', 'locations.id', 'left outer');
            $query->whereNotNull('origin_delivery_routes.origin_location_id');
        }

        if (isset($filters['destination_with_delivery_routes']) && $filters['destination_with_delivery_routes']) {
            $this->addJoin($joins, 'delivery_routes as destination_delivery_routes', 'locations.id', 'destination_delivery_routes.destination_location_id', 'left outer');
            $query->whereNotNull('destination_delivery_routes.destination_location_id');
        }

        if (isset($filters['origin_with_services']) && $filters['origin_with_services']) {
            $this->addJoin($joins, 'delivery_routes as origin_services', 'origin_services.origin_location_id', 'locations.id', 'left outer');
            $query->whereNotNull('origin_services.origin_location_id');
        }

        if (isset($filters['destination_with_services']) && $filters['destination_with_services']) {
            $this->addJoin($joins, 'delivery_routes as destination_services', 'locations.id', 'destination_services.destination_location_id', 'left outer');
            $query->whereNotNull('destination_services.destination_location_id');
        }

        if (isset($filters['country_id']) && $filters['country_id']) {
            $this->addJoin($joins, 'delivery_routes as destination_services', 'locations.id', 'destination_services.destination_location_id', 'left outer');
            $query->ofCountryId($filters['country_id']);
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['type_id']) && $filters['type_id']) {
            $query->ofType($filters['type_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('locations.code', 'asc');
    }

}