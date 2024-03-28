<?php

namespace App\Services;

use App\Models\VotingRoom;
use Exception;
use Illuminate\Support\Facades\Log;

class VotingRoomService
{
    public function deleteVotingRoom(VotingRoom $room): void
    {
        try {
            $room->delete();
        } catch (Exception $e) {
            Log::debug('Error deleting question: ' . $e->getMessage());
        }
    }
}
