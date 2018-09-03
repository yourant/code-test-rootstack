<?php

namespace App\Models;

use App\Presenters\MessagePresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $body
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User|null $user
 * @mixin \Eloquent
 */
class Message extends Model implements HasPresenter
{
    protected $fillable = ['user_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserFullName()
    {
        return $this->user ? $this->user->full_name : null;
    }

    public function getPresenterClass()
    {
        return MessagePresenter::class;
    }
}
