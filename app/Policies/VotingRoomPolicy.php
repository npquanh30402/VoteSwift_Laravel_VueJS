<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VotingRoom;

class VotingRoomPolicy
{
    public function viewResults(User $user, VotingRoom $room)
    {
        $session_name = "{$user->id}_voting_room_password_{$room->id}";
        $isOwner = $user->id == $room->user_id;
        if ($isOwner || session()->has($session_name)) {
            if ($isOwner)
                return true;
            if ($user->hasVotedForVotingRoom($room->id) && $room->settings->results_visibility === 'after_voting') {
                return true;
            }
        }

        return false;
    }

    public function view(User $user, VotingRoom $votingRoom): bool
    {
        return $user->id == $votingRoom->user_id;
    }

    public function update(User $user, VotingRoom $votingRoom): bool
    {
        return $user->id == $votingRoom->user_id;
    }

    public function delete(User $user, VotingRoom $votingRoom): bool
    {
        return $user->id == $votingRoom->user_id;
    }
}
