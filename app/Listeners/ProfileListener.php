<?php

namespace App\Listeners;

use App\Events\ProfileEvent;
use App\Notifications\ProfileNotification;
use Illuminate\Events\Dispatcher;


class ProfileListener
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
    public function handle(object $event): void
    {
        $event->user->notify(new ProfileNotification());
    }
    // public function subscribe(Dispatcher $events): void
    // {
    //     $events->listen(
    //         ProfileEvent::class,
    //         [ProfileListener::class, 'handle']
    //     );
    //     return[
    //         ProfileEvent::class => 'handle',
    //     ];
    // }
}
