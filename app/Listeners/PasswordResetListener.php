<?php

namespace App\Listeners;

use App\Events\ResetPasswordEvent;
use App\Events\VerficationEvent;
use App\Notifications\PasswordResetNotificaiton;

class PasswordResetListener
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
    public function handle(ResetPasswordEvent $event): void
    {
        $event->user->notify(new PasswordResetNotificaiton());
    }
}
