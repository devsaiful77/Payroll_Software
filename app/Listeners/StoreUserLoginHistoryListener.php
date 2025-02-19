<?php

namespace App\Listeners;

use App\Events\LoginHistoryEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use Carbon\Carbon;

class StoreUserLoginHistoryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\LoginHistoryEvent  $event
     * @return void
     */
    public function handle(LoginHistoryEvent $event)
    {
        $current_timestamp = Carbon::now()->toDateTimeString();              
        (new AuthenticationDataService())->insertLoginUserHistory($event->user, $current_timestamp);
        
    }
}
