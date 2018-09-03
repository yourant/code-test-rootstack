<?php
namespace App\Repositories;

use App\Models\CheckpointCode;
use Illuminate\Support\Collection;

class CheckpointCodeRepository extends AbstractRepository
{

    function __construct(CheckpointCode $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function findByTypeAndCode($type, $code)
    {
        return $this->model->ofType($type)->ofCode($code)->first();
    }

    public function search(array $params = [], $count = false)
    {
        $query = $this->model->select('checkpoint_codes.*','providers.name','classifications.order')
            ->distinct()
            ->join('providers', 'checkpoint_codes.provider_id', '=', 'providers.id')
            ->join('classifications', 'checkpoint_codes.classification_id', '=', 'classifications.id', 'left outer');

        $joins = collect();

        if (isset($params['id']) && $params['id']) {
            $query->ofId($params['id']);
        }

        if (isset($params['exclude_id']) && $params['exclude_id']) {
            $query->ofExcludeId($params['exclude_id']);
        }

        if (isset($params['virtual']) && $params['virtual']) {
            $query->ofVirtual();
        }

        if (isset($params['delivered']) && $params['delivered']) {
            $query->ofDelivered();
        }
        if (isset($params['returned']) && $params['returned']) {
            $query->ofReturned();
        }
        if (isset($params['canceled']) && $params['canceled']) {
            $query->ofCanceled();
        }
        if (isset($params['stalled']) && $params['stalled']) {
            $query->ofStalled();
        }
        if (isset($params['returning']) && $params['returning']) {
            $query->ofReturning();
        }
        if (isset($params['clockstop']) && $params['clockstop']) {
            $query->ofClockstop();
        }

        if (isset($params['type']) && $params['type']) {
            $query->ofType($params['type']);
        }

        if (isset($params['code']) && $params['code']) {
            $query->ofCode($params['code']);
        }

        if (isset($params['key']) && $params['key']) {
            $query->ofKey($params['key']);
        }

        if (isset($params['description']) && $params['description']) {
            $query->ofDescription($params['description']);
        }

        if (isset($params['category']) && $params['category']) {
            $query->ofCategory($params['category']);
        }

        if (isset($params['provider_id']) && $params['provider_id']) {
            $query->ofProviderId($params['provider_id']);
        }

        if (isset($params['provider_name']) && $params['provider_name']) {
            $query->ofProviderName($params['provider_name']);
        }

        if (isset($params['classification_id']) && $params['classification_id']) {
            $query->ofClassificationId($params['classification_id']);
        }

        if (isset($params['classification_type'])) {
            $query->ofClassificationType($params['classification_type']);
        }

        if (isset($params['classification_name'])) {
            $query->ofClassificationName($params['classification_name']);
        }

        if (isset($params['classification_leg'])) {
            $query->ofClassificationLeg($params['classification_leg']);
        }

        if (isset($params['without_classification'])) {
            $query->ofWithoutClassification($params['without_classification']);
        }

        if (isset($params['bag_id']) && $params['bag_id']) {
//                ->join('service_types', 'service_types.provider_id', '=', 'providers.id')
//                ->join('legs', 'legs.service_type_id', '=', 'service_types.id')
            $this->addJoin($joins,'provider_services', 'provider_services.provider_id','providers.id');
            $this->addJoin($joins,'legs', 'legs.provider_service_id', 'provider_services.id');
            $this->addJoin($joins,'delivery_routes','delivery_routes.id', 'legs.delivery_route_id');
            $this->addJoin($joins,'packages', 'packages.delivery_route_id',  'delivery_routes.id');
            $this->addJoin($joins,'bags', 'packages.bag_id',  'bags.id');
            $query->ofBagId($params['bag_id']);
        }

        if (isset($params['with_events'])) {
            $this->addJoin($joins,'checkpoint_code_event_code', 'checkpoint_code_event_code.checkpoint_code_id','checkpoint_codes.id', 'left outer');
            $query->whereNotNull('checkpoint_code_event_code.checkpoint_code_id');
        }

        if (isset($params['without_events'])) {
            $this->addJoin($joins,'checkpoint_code_event_code', 'checkpoint_code_event_code.checkpoint_code_id','checkpoint_codes.id', 'left outer');
            $query->whereNull('checkpoint_code_event_code.checkpoint_code_id');
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        $query->orderBy('providers.name')
            ->orderBy('classifications.order')
            ->orderBy('checkpoint_codes.key')
            ->orderBy('checkpoint_codes.description');

        return !$count ? $query : $query->count();
    }

    public function setEventCodes(CheckpointCode $checkpointCode, Collection $eventCodes)
    {
        return $checkpointCode->eventCodes()->sync($eventCodes->toArray());
    }

    public function unsetEventCodesFromCheckpointCodes(Collection $checkpoint_code_ids)
    {
        $checkpoint_code_ids->each(function ($checkpoint_code_id) {
            /** @var CheckpointCode $cc */
            if ($cc = $this->getById($checkpoint_code_id)) {
                $cc->eventCodes()->detach();
            }
        });
    }
}