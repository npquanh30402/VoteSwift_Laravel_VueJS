<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VotingRoom;
use Illuminate\Support\Facades\Cache;

class VotingRoomPolicy
{
    public function joinInvitation(User $user, VotingRoom $room, $token)
    {
        if ($user->id === $room->user_id) {
            return true;
        }

        $settings = $room->settings()->first();

        if ($settings && $settings->invitation_only) {
            $tokenCacheKey = "ballot.tkn.{$token}";
            $userId = Cache::get($tokenCacheKey);

            if ($userId === $user->id && $room->invitations()->where('invited_user_id', $userId)->exists()) {
                return true;
            }
        } else {
            return true;
        }

        return false;
    }

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
