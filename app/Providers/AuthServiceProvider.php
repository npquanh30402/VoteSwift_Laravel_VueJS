<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Candidate;
use App\Models\Question;
use App\Models\Vote;
use App\Models\VotingRoom;
use App\Policies\CandidatePolicy;
use App\Policies\QuestionPolicy;
use App\Policies\VotePolicy;
use App\Policies\VotingRoomPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        VotingRoom::class => VotingRoomPolicy::class,
        Question::class => QuestionPolicy::class,
        Candidate::class => CandidatePolicy::class,
        Vote::class => VotePolicy::class
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
