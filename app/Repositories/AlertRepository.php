<?php
namespace App\Repositories;

use App\Models\Alert;
use App\Models\Classification;
use App\Models\Provider;

class AlertRepository extends AbstractRepository
{

    function __construct(Alert $model)
    {
        $this->model = $model;
    }

    public function getByTypeAndSubtype($type, $subtype)
    {
        return $this->model->whereType($type)->whereSubtype($subtype)->first();
    }

    public function search(array $params = [], $count = false)
    {
        $query = $this->model
            ->select('alerts.*')
            ->join('providers', 'alerts.provider_id', '=', 'providers.id');

        if (isset($params['provider_id'])) {
            $query = $query->ofProviderId($params['provider_id']);
        }

        if (isset($params['name'])) {
            $query = $query->ofName($params['name']);
        }

        if (isset($params['type'])) {
            $query = $query->ofType($params['type']);
        }

        if (isset($params['subtype'])) {
            $query = $query->ofSubtype($params['subtype']);
        }

        return !$count ? $query->orderBy('providers.name')->orderBy('alerts.type')->orderBy('alerts.name') : $query->count();
    }

    public function addAlertDetail(Alert $alert, array $input)
    {
        return $alert->alertDetails()->create($input);
    }
}