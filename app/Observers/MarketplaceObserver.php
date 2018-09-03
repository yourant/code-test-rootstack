<?php

namespace App\Observers;

use App\Events\NotifyModelEvent;
use App\Models\Marketplace;

/**
 * Class MarketplaceObserver
 * @package App\Observers
 */
class MarketplaceObserver
{
    /**
     * @param Marketplace $marketplace
     */
    public function created(Marketplace $marketplace)
    {
        event(new NotifyModelEvent($marketplace, 'created'));
    }

    /**
     * @param Marketplace $marketplace
     */
    public function updated(Marketplace $marketplace)
    {
        event(new NotifyModelEvent($marketplace, 'updated'));
    }

    /**
     * @param Marketplace $marketplace
     */
    public function deleting(Marketplace $marketplace)
    {
        event(new NotifyModelEvent($marketplace, 'deleted'));
    }

}