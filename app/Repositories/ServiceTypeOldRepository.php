<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ServiceTypeOldRepository extends AbstractRepository
{

    function __construct(ServiceType $model)
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
            ->select('service_types.*')
            //->distinct()
            ->join('providers', 'service_types.provider_id', '=', 'providers.id')
            ->orderBy('providers.name', 'asc')
            ->orderBy('service_types.name', 'asc')
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
            ->select('service_types.*')
            ->addSelect('providers.name as provider_name')
            ->distinct()
            ->join('providers', 'service_types.provider_id', '=', 'providers.id');

        $joins = collect();

        if (isset($filters['provider_id'])) {
            $query->ofProviderId($filters['provider_id']);
        }

        if (isset($filters['code'])) {
            $query->ofCode($filters['code']);
        }

        if (isset($filters['provider_code'])) {
            $query->ofProviderCode($filters['provider_code']);
        }

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['client_id'])) {
            $this->addJoin($joins, 'legs', 'legs.service_type_id', 'service_types.id');
            $this->addJoin($joins, 'agreements', 'legs.agreement_id', 'agreements.id');
            $query->where('agreements.client_id', $filters['client_id']);
        }

        if (isset($filters['marketplace_id'])) {
            $this->addJoin($joins, 'legs', 'legs.service_type_id', 'service_types.id');
            $this->addJoin($joins, 'agreements', 'legs.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'client_marketplace', 'client_marketplace.client_id', 'agreements.client_id');
            $query->where('client_marketplace.marketplace_id', $filters['marketplace_id']);
        }

        if (isset($filters['country_id'])) {
            $this->addJoin($joins, 'legs', 'legs.service_type_id', 'service_types.id');
            $this->addJoin($joins, 'agreements', 'legs.agreement_id', 'agreements.id');
            $query->where('agreements.country_id', $filters['country_id']);
        }

        if (isset($params['first_checkpoint_newer_than'])) {
            $query->ofFirstCheckpointNewerThan($params['first_checkpoint_newer_than']);
        }

        if (isset($params['first_checkpoint_older_than'])) {
            $query->ofFirstCheckpointOlderThan($params['first_checkpoint_older_than']);
        }

        if (isset($filters['provider_service_type_key'])) {
            $this->addJoin($joins, 'provider_service_types', 'provider_service_types.id', 'service_types.provider_service_type_id');
            $query->ofProviderServiceTypeKey($filters['provider_service_type_key']);
        }

        if (isset($filters['service'])) {
            $query->ofService($filters['service']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query
            ->orderBy('provider_name', 'asc')
            ->orderBy('service_types.name', 'asc');
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
        if (array_key_exists('last_checkpoint_code_id', $attributes) && !$attributes['last_checkpoint_code_id']) {
            unset($attributes['last_checkpoint_code_id']);
        }

        return parent::update($model, $attributes);
    }

    public function getByType($type = 'last_mile', $params)
    {
        $query = $this->model
            ->select()
            ->addSelect(DB::raw('count(packages.id) as package_count'))
            ->join('agreements', 'legs.agreement_id', '=', 'agreements.id', 'left outer')
            ->join('providers', 'service_types.provider_id', '=', 'providers.id')
            ->join('legs', 'legs.service_type_id', '=', 'service_types.id', 'left outer');
    }
//    public function getEnabledForUser($user)
//    {
//        if ( ! $user->client) {
//            return $this->search()->get();
//        }
//
//        return $this->search(['client_id' => $user->client_id])->get();
//    }
//
//    public function getEnabledForClient(Client $client)
//    {
//
//        return $this->search(['client_id' => $client->id])->get();
//    }
//
//    public function getDefaultForUser($user)
//    {
//        if ( ! $user->client) {
//            return $this->search()->first();
//        }
//
//        return $this->search(['client_id' => $user->client_id])->first();
//    }
} 