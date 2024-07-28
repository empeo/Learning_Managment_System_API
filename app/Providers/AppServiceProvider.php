<?php

namespace App\Providers;

use App\Events\OrderEvent;
use App\Events\ProfileEvent;
use App\Events\ResetPasswordEvent;
use App\Events\SendOrderMessageEvent;
use App\Events\VerficationEvent;
use App\Listeners\EmailVerficarionRegisterListener;
use App\Listeners\OrderListener;
use App\Listeners\OrderMessageListener;
use App\Listeners\PasswordResetListener;
use App\Listeners\ProfileListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            OrderEvent::class,
            OrderListener::class
        );
        Event::listen(
            VerficationEvent::class,
            EmailVerficarionRegisterListener::class
        );
        Event::listen(
            ResetPasswordEvent::class,
            PasswordResetListener::class
        );
        Event::listen(
            ProfileEvent::class,
            ProfileListener::class
        );
    }
}
