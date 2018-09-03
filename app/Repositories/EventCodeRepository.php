<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 23/2/2018
 * Time: 11:36 AM
 */

namespace App\Repositories;


use App\Models\EventCode;
use Illuminate\Support\Collection;

class EventCodeRepository extends AbstractRepository
{
    function __construct(EventCode $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }

    public function search(array $params = [], $count = false)
    {
        $query = $this->model->select('event_codes.*')
                             ->distinct();

        $joins = collect();

        if (isset($params['key']) && $params['key']) {
            $query->ofKey($params['key']);
        }

        if (isset($params['description']) && $params['description']) {
            $query->ofDescription($params['description']);
        }

        if (isset($params['classification_type_checkpoint']) && $params['classification_type_checkpoint']) {
            $this->addJoin($joins,'checkpoint_code_event_code', 'checkpoint_code_event_code.event_code_id','event_codes.id');
            $this->addJoin($joins,'checkpoint_codes', 'checkpoint_code_event_code.checkpoint_code_id','checkpoint_codes.id');
            $this->addJoin($joins, 'classifications', 'checkpoint_codes.classification_id', 'classifications.id', 'left outer');
            $query->ofClassificationTypeCheckpoint($params['classification_type_checkpoint']);
        }

        if (isset($params['classification_name_checkpoint']) && $params['classification_name_checkpoint']) {
            $this->addJoin($joins,'checkpoint_code_event_code', 'checkpoint_code_event_code.event_code_id','event_codes.id');
            $this->addJoin($joins,'checkpoint_codes', 'checkpoint_code_event_code.checkpoint_code_id','checkpoint_codes.id');
            $this->addJoin($joins, 'classifications', 'checkpoint_codes.classification_id', 'classifications.id', 'left outer');
            $query->ofClassificationNameCheckpoint($params['classification_name_checkpoint']);
        }

        $joins->each(function ($item, $key) use (&$query) {
            $item = json_decode($item);
            $query->join($key, $item->first, '=', $item->second, $item->join_type);
        });

        $query->orderBy('event_codes.position');

        return !$count ? $query : $query->count();
    }

    public function setCheckpointCodes(EventCode $eventCode, Collection $checkpointCodes)
    {
        return $eventCode->checkpointCodes()->sync($checkpointCodes->toArray());
    }

    public function addCheckpointCodes(EventCode $eventCode, Collection $checkpointCodes)
    {
        return $eventCode->checkpointCodes()->syncWithoutDetaching($checkpointCodes->toArray());
    }
}