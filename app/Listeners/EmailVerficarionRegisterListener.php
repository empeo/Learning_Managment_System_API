<?php

namespace App\Listeners;

use App\Events\VerficationEvent;
use App\Notifications\EmailVerficationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmailVerficarionRegisterListener
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
    public function handle(VerficationEvent $event): void
    {
        $event->user->notify(new EmailVerficationNotification());
    }
}
