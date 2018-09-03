<?php
namespace App\Repositories;

use App\Models\CheckpointCode;
use App\Models\Classification;

class ClassificationRepository extends AbstractRepository
{

    function __construct(Classification $model)
    {
        $this->model = $model;
    }

    public function search(array $params = [], $count = false)
    {
        $query = $this->model->select('classifications.*');

        if (isset($params['key']) && $params['key']) {
            $query->ofKey($params['key']);
        }

        if (isset($params['type']) && $params['type']) {
            $query->ofType($params['type']);
        }

        if (isset($params['name']) && $params['name']) {
            $query->ofName($params['name']);
        }

        if (isset($params['leg']) && $params['leg']) {
            $query->ofLeg($params['leg']);
        }

        $query->orderBy('classifications.order');

        return ! $count ? $query : $query->count();
    }

    public function getByKey($key)
    {
        return $this->model->whereKey($key)->first();
    }

    public function addCheckpointCode(Classification $classification, CheckpointCode $checkpointCode)
    {
        return $classification->checkpointCodes()->save($checkpointCode);
    }
}