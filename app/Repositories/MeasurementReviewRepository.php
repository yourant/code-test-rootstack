<?php

namespace App\Repositories;

use App\Models\MeasurementReview;
use Illuminate\Support\Collection;

class MeasurementReviewRepository extends AbstractRepository
{
    function __construct(MeasurementReview $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search($params = [], $count = false, $distinct = true)
    {
        $joins = collect();

        $query = $this->model
            ->distinct()
            ->select('measurement_reviews.*');

        if ($distinct) {
            $query = $query->distinct();
        }

        if (isset($params['tracking']) && $params['tracking']) {
            $this->addJoin($joins, 'packages', 'measurement_reviews.package_id', 'packages.id');
            $query->where('packages.tracking_number', strtoupper($params['tracking']));
        }

        if (isset($params['client_id']) && $params['client_id']) {
            $this->addJoin($joins, 'packages', 'measurement_reviews.package_id', 'packages.id');
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'clients', 'agreements.client_id', 'clients.id');
            $query->where('clients.id', $params['client_id']);
        }

        if (isset($params['resolved']) && $params['resolved']) {
            $query->ofResolved();
        }

        if (isset($params['unresolved']) && $params['unresolved']) {
            $query->ofUnresolved();
        }

        if (isset($params['billable_method']) && $params['billable_method']) {
            $this->addJoin($joins, 'packages', 'measurement_reviews.package_id', 'packages.id');
            $query->where('packages.billable_method', $params['billable_method']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        if ($count) {
            return $query->count('measurement_reviews.id');
        }

        return $query;
    }
}