<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TariffTemplate
 *
 * @property int $id
 * @property int $service_id
 * @property string|null $valid_from
 * @property string|null $valid_to
 * @property int $enabled
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class TariffTemplate extends Model
{
    //
}
