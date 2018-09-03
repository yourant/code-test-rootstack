<?php

namespace App\Repositories;

use App\Models\Airport;

class AirportRepository extends AbstractRepository
{

    function __construct(Airport $model)
    {
        $this->model = $model;
    }

    public function getByCode($code)
    {
        return $this->search(compact('code'))->first();
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function search(array $filters = [], $distinct = true)
    {
        $query = $this->model->select('airports.*');

        if ($distinct) {
            $query->distinct();
        }

        if (isset($filters['code']) && $filters['code']) {
            $query->ofCode($filters['code']);
        }

        return $query->orderBy('airports.id', 'desc');
    }
}
