<?php

namespace App\Listeners;

use App\Events\NotifyModelEvent;
use App\Services\Observable\ObservableModelsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyShippingListener implements ShouldQueue
{
    public $queue = 'tracking-shipping-sync';

    protected $observableModelsService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ObservableModelsService $observableModelsService)
    {
        $this->observableModelsService = $observableModelsService;
    }

    /**
     * Handle the event.
     *
     * @param  NotifyModelEvent $event
     * @return void
     */
    public function handle(NotifyModelEvent $event)
    {
        $this->observableModelsService->notifyShipping($event->model, $event->action);
    }
}
