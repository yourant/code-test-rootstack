<?php

namespace App\Repositories;


use App\Models\EventCodeCategory;
use Illuminate\Support\Collection;

class EventCodeCategoryRepository extends AbstractRepository
{
    function __construct(EventCodeCategory $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search(array $params = [], $count = false)
    {
        $query = $this->model->select('event_code_categories.*')
                             ->distinct();

        $joins = collect();

        if (isset($params['name']) && $params['name']) {
            $query->ofName($params['name']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return !$count ? $query : $query->count();
    }
}