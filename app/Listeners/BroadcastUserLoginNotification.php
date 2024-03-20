<?php

namespace App\Listeners;

use App\Events\UserActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class BroadcastUserLoginNotification
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
    public function handle(Login $event): void
    {
        Log::debug($event->user->username . ' from BroadcastUserLoginNotification');

        broadcast(new UserActivity($event->user, "{$event->user->username} is online", 'success'))->toOthers();
    }
}
