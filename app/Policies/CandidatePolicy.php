<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function view(User $user, Candidate $candidate): bool
    {
        return $user->id === $candidate->question->room->user_id;
    }

    public function create(User $user, Candidate $candidate): bool
    {
        return $user->id === $candidate->question->room->user_id;
    }

    public function update(User $user, Candidate $candidate): bool
    {
        return $user->id === $candidate->question->room->user_id;
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->id === $candidate->question->room->user_id;
    }
}
