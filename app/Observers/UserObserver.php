<?php

namespace App\Observers;

use App\Events\NotifyModelEvent;
use App\Models\User;

/**
 * Class UserObserver
 * @package App\Observers
 */
class UserObserver
{
    /**
     * @param User $user
     */
    public function created(User $user)
    {
        event(new NotifyModelEvent($user, 'created'));
    }

    /**
     * @param User $user
     */
    public function updated(User $user)
    {
        event(new NotifyModelEvent($user, 'updated'));
    }

    /**
     * @param User $user
     */
    public function deleting(User $user)
    {
        event(new NotifyModelEvent($user, 'deleted'));
    }
}