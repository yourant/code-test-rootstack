<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 12/1/2018
 * Time: 4:35 PM
 */

namespace App\Repositories;

use App\Models\ProviderService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProviderServiceRepository extends AbstractRepository
{
    /** @var  DeliveryRouteRepository */
    protected $deliveryRouteRepository;

    function __construct(ProviderService $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->whereName($name)->first();
    }

    public function getAll()
    {
        return $this->model
            ->select('provider_services.*')
            ->addSelect('providers.name as provider_name')
            ->distinct()
            ->join('providers', 'provider_services.provider_id', '=', 'providers.id')
            ->orderBy('providers.name', 'asc')
            ->orderBy('provider_services.name', 'asc')
            ->get();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model
            ->select('provider_services.*')
            ->addSelect('providers.name as provider_name')
            ->distinct()
            ->join('providers', 'provider_services.provider_id', '=', 'providers.id');

        $joins = collect();

        if (isset($filters['provider_id'])) {
            $query->ofProviderId($filters['provider_id']);
        }

        if (isset($filters['name'])) {
            $query->ofName($filters['name']);
        }

        if (isset($filters['provider_code'])) {
            $query->ofProviderCode($filters['provider_code']);
        }

        if (isset($filters['client_id'])) {
            $this->addJoin($joins, 'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'delivery_routes', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'packages', 'packages.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $query->where('agreements.client_id', $filters['client_id']);
        }

        if (isset($filters['marketplace_id'])) {
            $this->addJoin($joins, 'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'delivery_routes', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'packages', 'packages.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'agreements.client_id');
            $query->where('client_marketplace.marketplace_id', $filters['marketplace_id']);
        }

        if (isset($filters['country_id'])) {
            $this->addJoin($joins, 'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'delivery_routes', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'packages', 'packages.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'locations as destination_locations', 'services.destination_location_id', 'destination_locations.id');
            $query->where('destination_locations.country_id', $filters['country_id']);
        }

        if (isset($filters['provider_service_type_key'])) {
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'provider_services.provider_service_type_id');
            $query->ofProviderServiceTypeKey($filters['provider_service_type_key']);
        }

        if (isset($filters['code'])) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['agreement_service_type_key']) && $filters['agreement_service_type_key']) {
            $this->addJoin($joins, 'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins, 'delivery_routes', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'packages', 'packages.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $this->addJoin($joins, 'service_types', 'service_types.id', 'services.service_type_id');
            $query->where('service_types.key', $filters['agreement_service_type_key']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query
            ->orderBy('providers.name', 'asc')
            ->orderBy('provider_services.name', 'asc');
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function getTypes()
    {
        return [
            'last_mile' => 'Last Mile',
            'transit'   => 'Transit',
            'others'    => 'Others',
        ];
    }

    public function update(Model $model, array $attributes)
    {
        return parent::update($model, $attributes);
    }

    public function getByType($type = 'last_mile', $params)
    {
        $query = $this->model
            ->select()
            ->addSelect(DB::raw('count(packages.id) as package_count'))
            ->join('legs', 'legs.provider_service_id', '=', 'provider_services.id', 'left outer')
            ->join('delivery_routes', 'legs.delivery_route_id', '=', 'delivery_routes.id', 'left outer')
            ->join('packages', 'packages.delivery_route_id', '=', 'delivery_routes.id', 'left outer');
    }

    public function recalculateLegsTransitDays(ProviderService $providerService)
    {
    }
}