<?php

namespace App\Services;

use App\Models\VotingRoom;
use App\Notifications\RoomCreation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendEmailVerificationNotification(Request $request): void
    {
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Exception $e) {
            Log::debug('Error sending email verification notification: ' . $e->getMessage());
        }
    }

    public function sendRoomCreationNotification(VotingRoom $room): void
    {
        try {
            $room->user->notify(new RoomCreation($room));
        } catch (Exception $e) {
            Log::debug('Error sending notification: ' . $e->getMessage());
        }
    }
}
