<?php

namespace App\Repositories;

use App\Models\Alias;

class AliasRepository extends AbstractRepository
{
    function __construct(Alias $model)
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
            ->select('aliases.*');

        if (isset($filters['code']) && $filters['code']) {
            $query = $query->ofCode($filters['code']);
        }

        return $query;
    }
}