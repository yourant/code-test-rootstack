<?php

namespace App\Observers;

use App\Events\NotifyModelEvent;
use App\Models\Service;

/**
 * Class ServiceObserver
 * @package App\Observers
 */
class ServiceObserver
{
    /**
     * @param Service $service
     */
    public function created(Service $service)
    {
        event(new NotifyModelEvent($service, 'created'));
    }

    /**
     * @param Service $service
     */
    public function updated(Service $service)
    {
        event(new NotifyModelEvent($service, 'updated'));
    }

    /**
     * @param Service $service
     */
    public function deleting(Service $service)
    {
        event(new NotifyModelEvent($service, 'deleted'));
    }
}