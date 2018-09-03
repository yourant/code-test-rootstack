<?php

namespace App\Repositories\Operation;

use App\Models\Operation\Frequency;
use App\Models\Operation\Panel;
use App\Repositories\AbstractRepository;
use App\Models\Operation\Batch;
use DB;
use Exception;
use Illuminate\Support\Facades\Redis;
use NinjaMutex\Lock\PredisRedisLock;
use NinjaMutex\Mutex;

class BatchRepository extends AbstractRepository
{
    function __construct(Batch $model)
    {
        $this->model = $model;
    }

    public function search(array $filters = [])
    {
        $filters = collect($filters);

        $query = $this->model
            ->select('operation_batches.*');


        if ($filters->has('created_at_older_than')) {
            $query->where('operation_batches.created_at', '<=', $filters->get('created_at_older_than'));
        }

        if ($filters->has('archived')) {
            $query->where('operation_batches.archived', $filters->get('archived'));
        }

        return $query->orderBy('operation_batches.created_at', 'desc');
    }

    public function getLastOfFrequencyAndValue(Frequency $frequency, $value)
    {
        return $this->model
            ->select('operation_batches.*')
            ->ofFrequencyId($frequency->id)
            ->ofValue($value)
            ->orderBy('operation_batches.value', 'desc')
            ->first();
    }

    public function getLastOfFrequency(Frequency $frequency)
    {
        return $this->model
            ->select('operation_batches.*')
            ->ofFrequencyId($frequency->id)
            ->orderBy('operation_batches.value', 'desc')
            ->first();
    }

    public function getLastReadyOfPanel(Panel $panel)
    {
        return $this->model
            ->select('operation_batches.*')
            ->ofPanelId($panel->id)
            ->ofReady()
            ->ofUnarchived()
            ->orderBy('operation_batches.created_at', 'desc')
            ->first();
    }

    public function archive(Batch $batch)
    {
        $this->deleteMetrics($batch);
        $this->deleteStateMilestoneMetrics($batch);
        $this->update($batch, ['archived' => true]);

        return true;
    }

    public function deleteMetrics(Batch $batch)
    {
        return $batch->metrics()->delete();
    }

    public function deleteStateMilestoneMetrics(Batch $batch)
    {
        return $batch->stateMilestoneMetrics()->delete();
    }

    public function incrementProcessed(Batch $batch, $step = 1)
    {
        $connection = Redis::connection()->client();
        $lock = new PredisRedisLock($connection);
        $mutex = new Mutex("batch_processed_{$batch->id}", $lock);
        if ($mutex->acquireLock()) {
            try {
                DB::beginTransaction();

                // Use pessimistic locking
                $b = $batch->fresh();
                $this->update($b, ['processed' => $b->processed + $step]);

                DB::commit();

                $mutex->releaseLock();

                return true;
            } catch (Exception $e) {
                DB::rollBack();

                $mutex->releaseLock();
                throw $e;
            }
        } else {
            throw new Exception('Unable to gain lock!');
        }
    }

    public function incrementTotal(Batch $batch, $step = 1)
    {
        $connection = Redis::connection()->client();
        $lock = new PredisRedisLock($connection);
        $mutex = new Mutex("batch_total_{$batch->id}", $lock);
        if ($mutex->acquireLock()) {
            try {
                DB::beginTransaction();

                // Use pessimistic locking
                $b = $batch->fresh();
                $this->update($b, ['total' => $b->total + $step]);

                DB::commit();

                $mutex->releaseLock();

                return true;
            } catch (Exception $e) {
                DB::rollBack();

                $mutex->releaseLock();
                throw $e;
            }
        } else {
            throw new Exception('Unable to gain lock!');
        }
    }
}
