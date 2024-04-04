<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    public function view(User $user, Question $question): bool
    {
        return $user->id === $question->room->user_id;
    }

    public function create(User $user, Question $question): bool
    {
        return $user->id === $question->room->user_id;
    }

    public function update(User $user, Question $question): bool
    {
        return $user->id === $question->room->user_id;
    }

    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->room->user_id;
    }
}
