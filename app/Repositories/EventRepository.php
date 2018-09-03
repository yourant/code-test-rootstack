<?php
/**
 * Created by PhpStorm.
 * User: plabin
 * Date: 12/1/2018
 * Time: 4:32 PM
 */

namespace App\Repositories;


use App\Models\Event;
use Illuminate\Support\Collection;

class EventRepository extends AbstractRepository
{
    function __construct(Event $model)
    {
        $this->model = $model;
    }

    private function addJoin(Collection &$joins, $table, $first, $second, $join_type = 'inner')
    {
        if (!$joins->has($table)) {
            $joins->put($table, json_encode(compact('first', 'second', 'join_type')));
        }
    }
}