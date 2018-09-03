<?php

namespace App\Repositories;


use App\Models\Prealert;
use App\Models\Package;
use App\Models\Provider;
use DB;
use Illuminate\Support\Collection;

class PrealertRepository extends AbstractRepository
{
    function __construct(Prealert $model)
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
            ->select('prealerts.*')
            ->distinct();

        $joins = collect();

        if (isset($filters['package_id'])) {
            $query->ofPackageId($filters['package_id']);
        }

        if (isset($filters['provider_id'])) {
            $query->ofProviderId($filters['provider_id']);
        }

        if (isset($filters['successful']) && $filters['successful']) {
            $query->ofSuccessful();
        }

        if (isset($filters['unsuccessful']) && $filters['unsuccessful']) {
            $query->ofUnsuccessful();
        }

        if (isset($filters['archived']) && $filters['archived']) {
            $query->ofArchived();
        }

        if (isset($filters['unarchived']) && $filters['unarchived']) {
            $query->ofUnarchived();
        }

        if (isset($filters['tracking']) && $filters['tracking']) {
            $this->addJoin($joins, 'packages', 'prealerts.package_id', 'packages.id');
            $query->ofTrackingNumber($filters['tracking']);
        }

        if (isset($filters['created_at_newer_than']) && $filters['created_at_newer_than']) {
            $query->ofCreatedAtAfterThan($filters['created_at_newer_than']);
        }

        if (isset($filters['created_at_older_than']) && $filters['created_at_older_than']) {
            $query->ofCreatedAtBeforeThan($filters['created_at_older_than']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query->orderBy('prealerts.id', 'desc');
    }

    public function countSuccessfulByPackageAndProvider(Package $package, Provider $provider)
    {
        $query = $this->search(['package_id' => $package->id, 'provider_id' => $provider->id])->ofSuccessful();

        return $query->count();
    }

    public function searchFailedPrealertsGroupByPackageAndProvider()
    {
        return $this->model
            ->select(['prealerts.package_id', 'prealerts.provider_id'])
            ->addSelect(DB::raw('sum(prealerts.success) as success_count'))
            ->addSelect(DB::raw('count(prealerts.id) as prealert_count'))
            ->addSelect(DB::raw('string_agg(distinct prealerts.errors, \',\') as errors'))
            ->groupBy(['prealerts.package_id', 'prealerts.provider_id'])
            ->havingRaw('sum(prealerts.success) = 0');
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
} 