<?php

namespace App\Listeners;

use App\Events\OrderEvent;
use App\Notifications\OrderMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderEvent $event): void
    {
        $event->order->user->notify(new OrderMessageNotification($event->order));
    }
}
