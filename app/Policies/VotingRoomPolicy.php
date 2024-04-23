<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VotingRoom;

class VotingRoomPolicy
{
    public function create(User $user, VotingRoom $votingRoom): bool
    {
        return $user->id === $votingRoom->user_id;
    }

    public function view(User $user, VotingRoom $votingRoom): bool
    {
        return $user->id === $votingRoom->user_id;
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
