<?php
namespace App\Repositories;

use App\Models\Client;
use App\Models\Tracker;
use Illuminate\Support\Collection;

class TrackerRepository extends AbstractRepository
{

    function __construct(Tracker $model)
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
            ->select('trackers.*');

        if (isset($filters['access_token']) && $filters['access_token']) {
            $query = $query->ofAccessToken($filters['access_token']);
        }

        return $query->orderBy('trackers.name', 'asc');
    }

    public function getByClient(Client $client)
    {
        return $this->search(['client_id' => $client->id])->get();
    }

    public function getByTracker(Tracker $tracker)
    {
        return $this->search(['tracker_id' => $tracker->id])->get();
    }

    public function getByAccessToken($access_token)
    {
        return $this->search(compact('access_token'))->first();
    }

    public function syncClients(Tracker $tracker, Collection $clients)
    {
        return $tracker->clients()->sync($clients->toArray());
    }
} 