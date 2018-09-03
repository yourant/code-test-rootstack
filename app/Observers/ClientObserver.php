<?php

namespace App\Observers;

use App\Events\NotifyModelEvent;
use App\Models\Client;

/**
 * Class ClientObservers
 * @package App\Observers
 */
class ClientObserver
{
    /**
     * @param Client $client
     */
    public function created(Client $client)
    {
        event(new NotifyModelEvent($client, 'created'));
    }

    /**
     * @param Client $client
     */
    public function updated(Client $client)
    {
        event(new NotifyModelEvent($client, 'updated'));
    }

    /**
     * @param Client $client
     */
    public function deleting(Client $client)
    {
        event(new NotifyModelEvent($client, 'deleted'));
    }
}