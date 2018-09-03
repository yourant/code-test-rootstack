<?php

namespace App\Repositories;

use App\Models\Agreement;
use App\Models\Client;
use Illuminate\Support\Collection;

class AgreementRepository extends AbstractRepository
{

    function __construct(Agreement $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model
            ->distinct('agreements.id')
            ->join('clients', 'agreements.client_id', '=', 'clients.id')
            ->select('agreements.*')
            ->addSelect('clients.name');

        $joins = collect();

        if (isset($filters['id']) && $filters['id']) {
            if (is_array($filters['id']) && !empty($filters['id'])) {
                return $query->whereIn('agreements.id', $filters['id']);
            } else {
                return !$filters['id'] ? $query : $query->where('agreements.id', $filters['id']);
            }
        }

        if (isset($filters['client_id']) && $filters['client_id']) {
            $query->ofClientId($filters['client_id']);
        }

        if (isset($filters['country_id']) && $filters['country_id']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'locations as destination_locations', 'destination_locations.id', 'services.destination_location_id', 'left outer');
            $query->ofDestinationCountryId($filters['country_id']);
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $query->ofServiceId($filters['service_id']);
        }

        if (isset($filters['service_transit_days']) && $filters['service_transit_days']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $query->ofServiceTransitDays($filters['service_transit_days']);
        }

        if (isset($filters['service_code']) && $filters['service_code']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $query->ofServiceCode($filters['service_code']);
        }

        if (isset($filters['provider_id']) && $filters['provider_id']) {
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $this->addJoin($joins, 'delivery_route_service', 'delivery_route_service.service_id', 'services.id');
            $this->addJoin($joins, 'delivery_routes', 'delivery_routes.id', 'delivery_route_service.delivery_route_id');
            $this->addJoin($joins, 'legs', 'legs.delivery_route_id', 'delivery_routes.id');
            $this->addJoin($joins, 'provider_services', 'provider_services.id', 'legs.provider_service_id');
            $this->addJoin($joins, 'providers', 'providers.id', 'provider_services.provider_id');
            if (is_array($filters['provider_id']) && !empty($filters['provider_id'])) {
                $query->whereIn('providers.id', $filters['provider_id']);
            } else {
                $query->where('providers.id', $filters['provider_id']);
            }


        }


        if (isset($filters['enabled']) && $filters['enabled']) {
            $query->where('agreements.enabled', true);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('clients.name', 'asc');
    }

    public function getEnabledForUser($user)
    {
        if (!$user->client) {
            return $this->search()->get();
        }

        return $this->getEnabledForClient($user->client);
    }

    public function getEnabledForClient(Client $client)
    {
        return $this->search(['client_id' => $client->id])->whereNull('agreements.deleted_at')->get();
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function disableClientToServices($client_id, $services_with_client_ids, $modification_services_with_client_ids)
    {
        foreach ($services_with_client_ids as $services_with_client_id){

            $services_with_client_id_exist = false;
            if(!empty($modification_services_with_client_ids)){
                $services_with_client_id_exist = in_array($services_with_client_id, $modification_services_with_client_ids);
            }

            if(!$services_with_client_id_exist || empty($modification_services_with_client_ids)){
                $this->disableAgreementByClientAndService($services_with_client_id, $client_id);
            }
        }
    }

    public function disableServiceToClients($service_id, $clients_with_service_ids, $modification_clients_with_service_ids)
    {
        foreach ($clients_with_service_ids as $clients_with_service_id){

            $clients_with_service_id_exist = false;
            if(!empty($modification_clients_with_service_ids)){
                $clients_with_service_id_exist = in_array($clients_with_service_id, $modification_clients_with_service_ids);
            }

            if(!$clients_with_service_id_exist || empty($modification_clients_with_service_ids)){
                $this->disableAgreementByClientAndService($service_id, $clients_with_service_id);
            }
        }
    }

    public function enableClientToServices($client_id, $services_with_client_ids, $modification_services_with_client_ids)
    {
        foreach ($modification_services_with_client_ids as $modification_services_with_client_id){
            if(!in_array($modification_services_with_client_id, $services_with_client_ids)){
                $this->enableAgreementByClientAndService($modification_services_with_client_id, $client_id);
            }
        }
    }

    public function enableServiceToClients($service_id, $clients_with_service_ids, $modification_clients_with_service_ids)
    {
        foreach ($modification_clients_with_service_ids as $modification_clients_with_service_id){
            if(!in_array($modification_clients_with_service_id, $clients_with_service_ids)){
                $this->enableAgreementByClientAndService($service_id, $modification_clients_with_service_id);
            }
        }
    }

    public function disableAgreementByClientAndService($service_id, $client_id)
    {
        /**@var Agreement $agreement */
        $agreement = $this->search([
            'client_id'  => $client_id,
            'service_id' => $service_id,
        ])->first();

        $this->update($agreement, [
            'enabled' => false
        ]);
    }

    public function enableAgreementByClientAndService($service_id, $client_id)
    {
        /**@var Agreement $agreement*/
        $agreement = $this->search([
            'service_id' => $service_id,
            'client_id'  => $client_id,
        ])->first();

        if($agreement){
            $this->update($agreement, [
                'enabled' => true
            ]);
        }
        else {
            $this->updateOrCreate([
                'service_id' => $service_id,
                'client_id' => $client_id,
                'enabled' => true
            ]);
        }
    }

} 