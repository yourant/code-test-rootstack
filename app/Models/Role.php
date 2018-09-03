<?php

namespace App\Models;

use Cartalyst\Sentinel\Roles\EloquentRole;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property array $permissions
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 */
class Role extends EloquentRole
{
    public function isClient()
    {
        return $this->slug == 'client';
    }

    public function isMarketPlace()
    {
        return $this->slug == 'marketplace';
    }
}
