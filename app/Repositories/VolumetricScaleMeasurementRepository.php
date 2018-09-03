<?php

namespace App\Repositories;

use App\Models\VolumetricScaleMeasurement;
use Illuminate\Support\Collection;

class VolumetricScaleMeasurementRepository extends AbstractRepository
{
    public function __construct(VolumetricScaleMeasurement $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search(array $filters = [])
    {
        $query = $this->model
            ->select('volumetric_scale_measurements.*')
            ->join('packages', 'volumetric_scale_measurements.package_id', '=', 'packages.id')
            ->distinct();

        $joins = collect();

        if (isset($filters['volumetric_scale_id']) && $filters['volumetric_scale_id']) {
            $query = $query->ofVolumetricScaleId($filters['volumetric_scale_id']);
        }

        if (isset($filters['client_id']) && $filters['client_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $client_id = $filters['client_id'];
            if (is_array($client_id) && !empty($client_id)) {
                $query->where(function ($q2) use ($client_id) {
                    collect($client_id)->each(function ($item) use ($q2) {
                        $q2->orWhere('agreements.client_id', $item);
                    });
                });
            } else {
                $query->where('agreements.client_id', $client_id);
            }
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $this->addJoin($joins, 'agreements', 'packages.agreement_id', 'agreements.id');
            $this->addJoin($joins, 'services', 'agreements.service_id', 'services.id');
            $service_id = $filters['service_id'];
            if (is_array($service_id) && !empty($service_id)) {
                $query->where(function ($q2) use ($service_id) {
                    collect($service_id)->each(function ($item) use ($q2) {
                        $q2->orWhere('agreements.service_id', $item);
                    });
                });
            } else {
                $query->where('agreements.service_id', $service_id);
            }
        }

        if (isset($filters['tracking']) && $filters['tracking']) {
            $tn = $filters['tracking'];
            if (is_array($tn) && !empty($tn)) {
                $query->where(function ($q2) use ($tn) {
                    collect($tn)->each(function ($item) use ($q2) {
                        $q2->orWhere('packages.tracking_number', strtoupper($item));
                    });
                });
            } else {
                $query->where('packages.tracking_number', strtoupper($tn));
            }
        }

        if (isset($filters['first_checkpoint_newer_than']) && $filters['first_checkpoint_newer_than']) {
            $query->where('packages.first_checkpoint_at', '>=', $filters['first_checkpoint_newer_than']);
        }

        if (isset($filters['first_checkpoint_older_than']) && $filters['first_checkpoint_older_than']) {
            $query->where('packages.first_checkpoint_at', '<=', $filters['first_checkpoint_older_than']);
        }


        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        return $query;
    }
}