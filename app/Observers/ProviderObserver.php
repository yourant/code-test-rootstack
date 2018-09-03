<?php

namespace App\Observers;

use App\Events\NotifyModelEvent;
use App\Models\Provider;

/**
 * Class ProviderObserver
 * @package App\Observers
 */
class ProviderObserver
{
    /**
     * @param Provider $provider
     */
    public function created(Provider $provider)
    {
        event(new NotifyModelEvent($provider, 'created'));
    }

    /**
     * @param Provider $provider
     */
    public function updated(Provider $provider)
    {
        event(new NotifyModelEvent($provider, 'updated'));
    }

    /**
     * @param Provider $provider
     */
    public function deleting(Provider $provider)
    {
        event(new NotifyModelEvent($provider, 'deleted'));
    }
}