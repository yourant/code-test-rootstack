<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Download
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $hash
 * @property string|null $filename
 * @property string|null $bucket
 * @property string $filepath
 * @property int $download_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User|null $user
 * @mixin \Eloquent
 */
class Download extends Model
{
    protected $fillable = ['user_id', 'hash', 'filename', 'bucket', 'filepath'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
