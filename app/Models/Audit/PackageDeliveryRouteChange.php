<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;

class PackageDeliveryRouteChange extends Model
{
    protected $table = 'audit_package_delivery_route_changes';

    protected $fillable = [
        'package_id',
        'old_delivery_route',
        'new_delivery_route',
        'user_id',
        'user_email'
    ];
}
