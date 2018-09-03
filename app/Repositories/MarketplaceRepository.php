<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\Marketplace;
use Illuminate\Support\Collection;

class MarketplaceRepository extends AbstractRepository
{

    function __construct(Marketplace $model)
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
            ->distinct()
            ->select('marketplaces.*');

        if (isset($filters['name'])) {
            $query->ofName($filters['name']);
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['access_token']) && $filters['access_token']) {
            $query = $query->ofAccessToken($filters['access_token']);
        }

        return $query->orderBy('marketplaces.name', 'asc');
    }

    public function getByName($name)
    {
        return $this->search(compact('name'))->first();
    }

    public function getByCode($code)
    {
        return $this->search(compact('code'))->first();
    }

    public function getByAccessToken($access_token)
    {
        return $this->search(compact('access_token'))->first();
    }

    public function syncClients(Marketplace $marketplace, Collection $clients)
    {
        return $marketplace->clients()->sync($clients->toArray());
    }
} 