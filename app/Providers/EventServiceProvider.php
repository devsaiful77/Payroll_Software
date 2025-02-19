<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Events\LoginHistoryEvent;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Listeners\StoreUserLoginHistoryListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
            LoginHistoryEvent::class => [
            StoreUserLoginHistoryListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
