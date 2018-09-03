<?php

namespace App\Observers;

use App\Events\NotifyModelEvent;
use App\Models\Agreement;

/**
 * Class AgreementObserver
 * @package App\Observers
 */
class AgreementObserver
{
    /**
     * @param Agreement $agreement
     */
    public function created(Agreement $agreement)
    {
        event(new NotifyModelEvent($agreement, 'created'));
    }

    /**
     * @param Agreement $agreement
     */
    public function updated(Agreement $agreement)
    {
        event(new NotifyModelEvent($agreement, 'updated'));
    }

    /**
     * @param Agreement $agreement
     */
    public function deleting(Agreement $agreement)
    {
        event(new NotifyModelEvent($agreement, 'deleted'));
    }
}