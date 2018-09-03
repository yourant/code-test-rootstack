<?php
namespace App\Repositories;

use App\Models\AlertDetail;

class AlertDetailRepository extends AbstractRepository
{

    function __construct(AlertDetail $model)
    {
        $this->model = $model;
    }

    public function search(array $params = [], $count = false)
    {
        $query = $this->model
            ->select('alert_details.*')
            ->join('classifications', 'alert_details.classification_id', '=', 'classifications.id');

        if (isset($params['alert_id'])) {
            $query = $query->ofAlertId($params['alert_id']);
        }

        if (isset($params['classification_id'])) {
            $query = $query->ofClassificationId($params['classification_id']);
        }

        return !$count ? $query->orderBy('classifications.order') : $query->count();
    }
}