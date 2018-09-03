<?php

namespace App\Repositories\Operation;

use App\Repositories\AbstractRepository;
use App\Models\Operation\Frequency;

class FrequencyRepository extends AbstractRepository
{
    function __construct(Frequency $model)
    {
        $this->model = $model;
    }

    public function getByKey($key)
    {
        return $this->model->where('operation_frequencies.key', $key)->first();
    }
}

