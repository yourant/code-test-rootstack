<?php

namespace App\Repositories;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Support\Collection;

class ProviderRepository extends AbstractRepository
{
    function __construct(Provider $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->whereName($name)->first();
    }

    public function getByCode($code)
    {
        return $this->search(compact('code'))->first();
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }


    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model
            ->select('providers.*')
            ->distinct();

        $joins = collect();

        if (isset($filters['country_id']) && $filters['country_id']) {
            $query->ofCountryId($filters['country_id']);
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['name']) && $filters['name']) {
            $query->ofName($filters['name']);
        }

        if (isset($filters['provider_service_key']) && $filters['provider_service_key']) {
//            $query
//                ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
//                ->join('provider_service_types', 'provider_service_types.id', '=', 'provider_services.provider_service_type_id')
//                ->where('provider_service_types.key', $filters['provider_service_key']);

            $this->addJoin($joins, 'provider_services', 'provider_services.provider_id', 'providers.id');
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'provider_services.provider_service_type_id');

            $query->where('provider_service_types.key', $filters['provider_service_key']);

        }

        if (isset($filters['service_id']) && $filters['service_id']) {
//            $query->join('provider_services', 'provider_services.provider_id', '=', 'providers.id');
//            $query->join('legs', 'legs.provider_service_id', '=', 'provider_services.id');
//            $query->join('delivery_routes', 'delivery_routes.id', '=', 'legs.delivery_route_id');
//            $query->join('delivery_route_service', 'delivery_route_service.delivery_route_id', '=', 'delivery_routes.id');
//            $query->join('services', 'services.id', '=', 'delivery_route_service.service_id');


            $this->addJoin($joins,'provider_services', 'provider_services.provider_id', 'providers.id');
            $this->addJoin($joins,'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins,'delivery_routes', 'delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins,'delivery_route_service', 'delivery_route_service.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins,'services', 'services.id','delivery_route_service.service_id');

            if (is_array($filters['service_id']) && !empty($filters['service_id'])) {
                $query->whereIn('services.id', $filters['service_id']);
            } else {
                !$filters['service_id'] ? $query : $query->where('services.id', $filters['service_id']);
            }
        }

        if (isset($filters['transit_days']) && $filters['transit_days']) 
        {
            $this->addJoin($joins,'provider_services', 'provider_services.provider_id', 'providers.id');
            $query->where('provider_services.transit_days', $filters['transit_days']);

//            $query
//                ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
//                ->where('provider_services.transit_days', $filters['transit_days']);
        }

        if (isset($filters['origin_location_id']) && $filters['origin_location_id']) {
//            $query
//                ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
//                ->join('legs', 'legs.provider_service_id', '=', 'provider_services.id')
//                ->join('delivery_routes', 'delivery_routes.id', '=', 'legs.delivery_route_id')
//                ->join('locations as routes_origin_location', 'routes_origin_location.id','=','delivery_routes.origin_location_id')
//                ->where('routes_origin_location.id', $filters['origin_location_id']);

            $this->addJoin($joins,'provider_services', 'provider_services.provider_id', 'providers.id');
            $this->addJoin($joins,'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins,'delivery_routes', 'delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins,'location as routes_origin_location', 'routes_origin_location.id', 'delivery_routes.origin_location_id');
            $query->where('routes_origin_location.id', $filters['origin_location_id']);

        }

        if (isset($filters['destination_location_id']) && $filters['destination_location_id']) {
//            $query
//                ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
//                ->join('legs', 'legs.provider_service_id', '=', 'provider_services.id')
//                ->join('delivery_routes', 'delivery_routes.id', '=', 'legs.delivery_route_id')
//                ->join('locations as routes_destination_location', 'routes_destination_location.id','=','delivery_routes.destination_location_id')
//                ->where('routes_destination_location.id', $filters['destination_location_id']);

            $this->addJoin($joins,'provider_services', 'provider_services.provider_id', 'providers.id');
            $this->addJoin($joins,'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins,'delivery_routes', 'delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins,'location as routes_destination_location', 'routes_destination_location.id', 'delivery_routes.origin_location_id');
            $query->where('routes_destination_location.id', $filters['destination_location_id']);
        }

        if (isset($filters['total_transit_days']) && $filters['total_transit_days']) {
//            $query
//                ->join('provider_services', 'provider_services.provider_id', '=', 'providers.id')
//                ->join('legs', 'legs.provider_service_id', '=', 'provider_services.id')
//                ->join('delivery_routes', 'delivery_routes.id', '=', 'legs.delivery_route_id')
//                ->where('delivery_routes.total_transit_days', $filters['total_transit_days']);

            $this->addJoin($joins,'provider_services', 'provider_services.provider_id', 'providers.id');
            $this->addJoin($joins,'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins,'delivery_routes', 'delivery_routes.id', 'legs.delivery_route_id');
            $query->where('delivery_routes.total_transit_days', $filters['total_transit_days']);

        }

        if (isset($filters['generic']) && $filters['generic']) {
            $query->OfGeneric();
        }

        if (isset($filters['parent_id']) && $filters['parent_id']) {
            $query->where('parent_id', '=', $filters['parent_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('providers.name', 'asc');
    }

//    public function getServiceTypes(Provider $provider)
//    {
//        return $provider->serviceTypes;
//    }

    public function getProviderServices(Provider $provider)
    {
        return $provider->providerServices;
    }

//    public function addServiceType(Provider $provider, array $attributes)
//    {
//        if (array_key_exists('last_checkpoint_code_id', $attributes) && !$attributes['last_checkpoint_code_id']) {
//            unset($attributes['last_checkpoint_code_id']);
//        }
//        return $provider->serviceTypes()->create($attributes);
//    }

    public function addProviderService(Provider $provider, array $attributes)
    {
        return $provider->providerServices()->create($attributes);
    }
} 