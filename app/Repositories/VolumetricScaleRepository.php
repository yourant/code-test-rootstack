<?php

namespace App\Repositories;

use App\Models\VolumetricScale;

class VolumetricScaleRepository extends AbstractRepository
{
    public function __construct(VolumetricScale $model)
    {
        $this->model = $model;
    }

    public function search(array $filters = [])
    {
        $query = $this->model;

        if (isset($filters['code']) && $filters['code']) {
            $query = $query->ofCode($filters['code']);
        }

        return $query;
    }

    public function getByCode($code)
    {
        return $this->search(compact('code'))->first();
    }
}