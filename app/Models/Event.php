<?php

namespace App\Models;

use App\Presenters\EventPresenter;
use Illuminate\Database\Eloquent\Model;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Class Event
 *
 * @package App
 * @property EventCode $eventCode
 * @property CheckpointCode $checkpointCode
 * @property Checkpoint $lastCheckpoint
 * @property Package $package
 * @mixin \Eloquent
 */
class Event extends Model implements HasPresenter
{
    protected $fillable = [
        'package_id',
        'event_code_id',
        'last_checkpoint_id',
    ];

    protected $with = ['eventCode'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function eventCode()
    {
        return $this->belongsTo(EventCode::class);
    }

    public function lastCheckpoint()
    {
        return $this->belongsTo(Checkpoint::class, 'last_checkpoint_id');
    }

    public function checkpoints()
    {
        $relation = $this->hasManyThrough(Checkpoint::class, CheckpointCode::class, 'id', 'checkpoint_code_id');

        $relation->getQuery()
            ->join("checkpoint_code_event_code", "checkpoint_code_event_code.checkpoint_code_id", '=', "checkpoint_codes.id")
            ->join("event_codes", "checkpoint_code_event_code.event_code_id", '=', "event_codes.id")
            ->join("events", "events.event_code_id", '=', "event_codes.id")
            ->whereColumn('checkpoints.package_id', '=', 'events.package_id')
            ->select('checkpoints.*')
            ->orderByDesc('checkpoints.checkpoint_at');

        // It's a kind of magic...
        $basic_where = $relation->getQuery()->getQuery()->wheres[0];
        $basic_where['column'] = 'events.id';
        $relation->getQuery()->getQuery()->wheres[0] = $basic_where;

        return $relation;
    }

    public function getEventCodeDescription()
    {
        return $this->eventCode ? $this->eventCode->description : null;
    }

    public function getEventCode()
    {
        return $this->eventCode ? $this->eventCode : null;
    }

    public function getEventCodeId()
    {
        return $this->eventCode ? $this->eventCode->id : null;
    }

    public function getEventCodeKey()
    {
        return $this->eventCode ? $this->eventCode->key : null;
    }

    public function isDelivered()
    {
        return $this->getEventCodeDescription() == 'Delivered';
    }

    public function getLastCheckpointCheckpointCodeDescription()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->getCheckpointCodeDescription() : null;
    }

    public function getLastCheckpointDetails()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->details : null;
    }

    public function getLastCheckpointCity()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->city : null;
    }

    public function getLastCheckpointOffice()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->office : null;
    }

    public function getLastCheckpointReceivedBy()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->received_by : null;
    }

    public function getLastCheckpointOfficeZip()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->office_zip : null;
    }

    public function getLastCheckpointCreatedAt()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->created_at : null;
    }

    public function getLastCheckpointCheckpointAt()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->checkpoint_at : null;
    }

    public function getLastCheckpointTimezoneName()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->getTimezoneName() : null;
    }

    public function getLastCheckpointTimezoneDescription()
    {
        return $this->lastCheckpoint ? $this->lastCheckpoint->getTimezoneDescription() : null;
    }

    public function getPresenterClass()
    {
        return EventPresenter::class;
    }
}
