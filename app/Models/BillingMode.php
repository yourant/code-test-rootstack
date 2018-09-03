<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingMode extends Model
{
    protected $fillable = ['key', 'description'];

    public function isVolumetric()
    {
        return $this->key == 'volumetric_weight';
    }
}
