<?php

namespace App\Repositories;

use App\Models\Currency;

class CurrencyRepository extends AbstractRepository
{

    function __construct(Currency $model)
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
            ->select('currencies.*')
            ->distinct();

        return $query->orderBy('currencies.code', 'asc');
    }

    public function getByCode($code)
    {
        return $this->model->where('currencies.code', strtoupper($code))->first();
    }
}