<?php

namespace App\Listeners;

use App\Events\UserActivity;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class BroadcastUserLogoutNotification
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
    public function handle(Logout $event): void
    {
        Log::debug($event->user->username . ' from BroadcastUserLogoutNotification');
        broadcast(new UserActivity($event->user, "{$event->user->username} is offline", 'danger'))->toOthers();
    }
}
