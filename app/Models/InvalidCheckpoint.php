<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InvalidCheckpoint
 * @package App
 */
class InvalidCheckpoint extends Model
{
    protected $fillable = ['package_id', 'key', 'description', 'checkpoint_at', 'sent_at'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeOfUnsent($query)
    {
        $query->whereNull('invalid_checkpoints.sent_at');
    }

    public function getPackageTrackingNumber()
    {
        return $this->package ? $this->package->tracking_number : null;
    }

}