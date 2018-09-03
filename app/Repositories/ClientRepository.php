<?php
namespace App\Repositories;

use App\Models\Client;
use Illuminate\Support\Collection;

class ClientRepository extends AbstractRepository
{

    function __construct(Client $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function getByName($name)
    {
        return $this->model->ofName($name)->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [])
    {
        $query = $this->model
            ->distinct()
            ->select('clients.*');

        $joins = collect();
        
        if (isset($filters['access_token']) && $filters['access_token']) {
            $query->ofAccessToken($filters['access_token']);
        }

        if (isset($filters['client_code']) && $filters['client_code']) {
            $query->ofCode($filters['client_code']);
        }

        if (isset($filters['client_name']) && $filters['client_name']) {
            $query->ofName($filters['client_name']);
        }

        if (isset($filters['marketplace_id']) && $filters['marketplace_id']) {
            $this->addJoin($joins, 'client_marketplace', 'clients.id', 'client_marketplace.client_id');
            $query->ofMarketplaceId($filters['marketplace_id']);
        }

        if (isset($filters['country_id']) && $filters['country_id']) {
            $this->addJoin($joins, 'countries', 'countries.id', 'clients.country_id');
            $query->ofCountryId($filters['country_id']);
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $this->addJoin($joins, 'agreements', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $query->where('agreements.enabled', '=' ,true);
            $query->ofServiceId($filters['service_id']);
        }

        if (isset($filters['exclude_client_id']) && $filters['exclude_client_id']) {
            $this->addJoin($joins, 'agreements', 'agreements.client_id', 'clients.id');
            $this->addJoin($joins, 'services', 'services.id', 'agreements.service_id');
            $query->ofExcludeClientId($filters['exclude_client_id']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('clients.name', 'asc');
    }

    public function getByAccessToken($access_token)
    {
        return $this->search(compact('access_token'))->first();
    }

    public function getSubaccounts(Client $client)
    {
        return $client->marketplaces;
    }
} 