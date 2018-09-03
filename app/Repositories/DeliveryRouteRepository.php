<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 12/1/2018
 * Time: 4:32 PM
 */

namespace App\Repositories;

use App\Models\DeliveryRoute;
use App\Models\Package;
use Illuminate\Support\Collection;

class DeliveryRouteRepository extends AbstractRepository
{
    function __construct(DeliveryRoute $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search(array $filters = [])
    {
        $query = $this->model
            ->distinct('delivery_routes.id')
            ->select('delivery_routes.*');

        $joins = collect();

        if (isset($filters['id']) && $filters['id']) {
            $query->ofId($filters['id']);
        }

        if (isset($filters['origin_location_id']) && $filters['origin_location_id']) {
            $this->addJoin($joins, 'locations as origin_location', 'origin_location.id', 'delivery_routes.origin_location_id');
            $query->ofOriginLocationId($filters['origin_location_id']);
        }

        if (isset($filters['destination_location_id']) && $filters['destination_location_id']) {
            $this->addJoin($joins, 'locations as destination_location', 'destination_location.id', 'delivery_routes.destination_location_id');
            $query->ofDestinationLocationId($filters['destination_location_id']);
        }

        if (isset($filters['provider_service_id']) && $filters['provider_service_id']) {
            $this->addJoin($joins, 'legs', 'delivery_routes.id', 'legs.delivery_route_id');
            $query->ofProviderServiceId($filters['provider_service_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('delivery_routes.total_transit_days', 'asc');
    }

    public function getLegs(DeliveryRoute $deliveryRoute)
    {
        return $deliveryRoute->legs;
    }

    public function getAvailableDeliveryRoutesForPackage(Package $package)
    {
        $delivery_routes = $this->search([
            'origin_location_id' => $package->getAgreementServiceOriginLocationId(),
            'destination_location_id' => $package->getAgreementServiceDestinationLocationId(),
            'enabled' => true
        ])->with(['legs'])->get();
        
        return $delivery_routes;
    }

    public function getAvailableDeliveryRoutes()
    {
        $delivery_routes = $this->search([
            'enabled' => true
        ])->with(['legs'])->get();

        return $delivery_routes;
    }
}