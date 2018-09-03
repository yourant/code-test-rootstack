<?php

namespace App\Repositories;

use App\Models\Dispatch;
use Illuminate\Support\Collection;

class DispatchRepository extends AbstractRepository
{

    function __construct(Dispatch $model)
    {
        $this->model = $model;
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
//        $query = $this->model
//            ->select('dispatches.*')
//            ->distinct()
//            ->join('bags', 'dispatches.id', '=', 'bags.dispatch_id')
//            ->join('packages', 'bags.id', '=', 'packages.bag_id')
//            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id');

        $joins = collect();

        $query = $this->model
            ->distinct()
            ->select('dispatches.*')
            ->join('bags', 'dispatches.id', '=', 'bags.dispatch_id')
            ->join('packages', 'bags.id', '=', 'packages.bag_id')
            ->join('agreements', 'packages.agreement_id', '=', 'agreements.id');

        if (isset($filters['id'])) {
            $query->ofId($filters['id']);
        }

        if (isset($filters['exclude_ids'])) {
            $query->ofExcludeIds($filters['exclude_ids']);
        }

        if (isset($filters['client_id']) && $filters['client_id']) {
            $query->ofClientId($filters['client_id']);
        }

        if (isset($filters['agreement_id']) && $filters['agreement_id']) {
            $query->ofAgreementId($filters['agreement_id']);
        }

        if (isset($filters['agreement_type']) && $filters['agreement_type']) {
            $query->ofAgreementType($filters['agreement_type']);
        }


        if (isset($filters['country_id']) && $filters['country_id']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'locations as destination_location', 'destination_location.id', 'services.destination_location_id', 'left outer');

            $query->ofDestinationCountryId($filters['country_id']);
        }


        if (isset($filters['marketplace_id']) && $filters['marketplace_id']) {
//            $query
//                ->join('client_marketplace', 'client_marketplace.client_id', '=', 'clients.id')
//                ->ofMarketplaceId($filters['marketplace_id']);

            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'clients.id');
            $query = $query->ofMarketplaceId($filters['marketplace_id']);
        }

//        if (isset($filters['created_at_greater_than']) && $filters['created_at_greater_than']) {
//            $query->ofCreateAt($filters['created_at_greater_than']);
//        }

        if (isset($filters['year'])) {
            $query->ofYear($filters['year']);
        }

        if (isset($filters['code'])) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['cn38'])) {
            $query->ofCn38($filters['cn38']);
        }

        if (isset($filters['country_code']) && $filters['country_code']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'locations as destination_location', 'destination_location.id', 'services.destination_location_id', 'left outer');
            $this->addJoin($joins, 'countries', 'countries.id', 'destination_location.country_id');
            $query = $query->ofCountryCode($filters['country_code']);
        }

        if (isset($filters['provider_code']) && $filters['provider_code']) {
            $this->addJoin($joins, 'delivery_routes', 'delivery_routes.id', 'packages.delivery_route_id');
            $this->addJoin($joins, 'legs', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'provider_services', 'provider_services.id', 'legs.provider_service_id');
            $this->addJoin($joins, 'providers', 'providers.id', 'provider_services.provider_id');
            $query = $query->ofProviderCode($filters['provider_code']);
        }

        if (isset($filters['provider_key']) && $filters['provider_key']) {
            $this->addJoin($joins, 'delivery_routes', 'delivery_routes.id', 'packages.delivery_route_id');
            $this->addJoin($joins, 'legs', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'provider_services', 'provider_services.id', 'legs.provider_service_id');
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'provider_services.provider_service_type_id');
            $query = $query->ofProviderKey($filters['provider_key']);
        }


        if (isset($filters['number_greater_than']) && $filters['number_greater_than']) {
            $query->ofCreateAt($filters['number_greater_than'], '>=');
        }

        if (isset($filters['number_lower_than']) && $filters['number_lower_than']) {
            $query->ofCreateAt($filters['number_lower_than'], '<=');
        }

//        if (isset($filters['air_waybill_id']) && $filters['air_waybill_id']) {
        if (array_key_exists('air_waybill_id', $filters)) {
            $query->ofAirWaybillId($filters['air_waybill_id']);
        }

        if (isset($filters['provider_invoices_id']) && $filters['provider_invoices_id']) {

            $this->addJoin($joins, 'package_provider_invoice', 'packages.id', 'package_provider_invoice.package_id');

            $query->where('package_provider_invoice.provider_invoice_id', $filters['provider_invoices_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('dispatches.code', 'desc');
    }

    public function getTotalWeight(Dispatch $dispatch)
    {
        $query = $this->model
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->where('dispatches.id', $dispatch->id);

        return $query->sum('packages.weight');
    }

    public function getPackageCount(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->where('dispatches.id', $dispatch->id);

        return $query->count('packages.id');
    }

    public function getBagCount(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->where('dispatches.id', $dispatch->id);

        return $query->count('bags.id');
    }

    public function getCheckedInCount(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('dispatches.id', $dispatch->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCS');

        return $query->count('packages.id');
    }

    public function getCheckedInDate(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->select('checkpoints.checkpoint_at')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('dispatches.id', $dispatch->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCS')
            ->orderBy('checkpoints.checkpoint_at', 'asc');

        return $query->limit(1)->get()->pluck('checkpoint_at')->first();
    }

    public function getConfirmedCount(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('dispatches.id', $dispatch->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCF');

        return $query->count('packages.id');
    }

    public function getConfirmedDate(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->select('checkpoints.checkpoint_at')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('dispatches.id', $dispatch->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'RCF')
            ->orderBy('checkpoints.checkpoint_at', 'asc');

        return $query->limit(1)->get()->pluck('checkpoint_at')->first();
    }

    public function getDeliveredCount(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('dispatches.id', $dispatch->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'DLV');

        return $query->count('packages.id');
    }

    public function getDeliveredDate(Dispatch $dispatch)
    {
        $query = $this->model
            ->distinct()
            ->select('checkpoints.checkpoint_at')
            ->join('bags', 'bags.dispatch_id', '=', 'dispatches.id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('checkpoints', 'checkpoints.package_id', '=', 'packages.id')
            ->join('checkpoint_codes', 'checkpoints.checkpoint_code_id', '=', 'checkpoint_codes.id')
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->where('dispatches.id', $dispatch->id)
            ->where('providers.code', 'PR5573')
            ->where('checkpoint_codes.type', 'DLV')
            ->orderBy('checkpoints.checkpoint_at', 'asc');

        return $query->limit(1)->get()->pluck('checkpoint_at')->first();
    }

    public function getDispatchByProvider($provider_id = '')
    {
        $query = $this->model
            ->distinct()
            ->join('bags', 'dispatches.id', '=', 'bags.dispatch_id')
            ->join('packages', 'packages.bag_id', '=', 'bags.id')
            ->join('delivery_routes', 'delivery_routes.id', '=', 'packages.delivery_route_id')
            ->join('legs', 'legs.delivery_route_id', '=', 'delivery_routes.id')
            ->join('provider_services', 'provider_services.id', '=', 'legs.provider_service_id')
            ->join('providers', 'providers.id', '=', 'provider_services.provider_id');

        if ($provider_id) {
            $query->where('providers.id', $provider_id);
        }

        $query->select('dispatches.*');

        return $query->orderBy('dispatches.year', 'desc');
    }
}