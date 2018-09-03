<?php
namespace App\Repositories;

use App\Models\Message;

class MessageRepository extends AbstractRepository {

    function __construct(Message $model)
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
            ->distinct()
            ->select('messages.*');

        return $query->orderBy('messages.created_at', 'desc');
    }
} 