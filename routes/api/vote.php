<?php

use App\Http\Controllers\Api\VoteController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::prefix('/rooms/{room}')->group(function () {
        // Voting operations
        Route::get('/votes/start', [VoteController::class, 'startVote'])->name('api.rooms.votes.start');
        Route::post('/votes/vote', [VoteController::class, 'store'])->name('api.rooms.votes.store');

        // Results and choices
        Route::get('/votes/results', [VoteController::class, 'getVoteResults'])->name('api.rooms.votes.results');
        Route::get('/votes/choices', [VoteController::class, 'getUserChoices'])->name('api.rooms.votes.choices.get');
        Route::post('/votes/choices', [VoteController::class, 'storeUserChoices'])->name('api.rooms.votes.choices.store');
        Route::post('/votes/choice', [VoteController::class, 'broadcastChoice'])->name('api.rooms.votes.choice.broadcast');
        Route::delete('/votes/choices', [VoteController::class, 'deleteUserChoices'])->name('api.rooms.votes.choices.delete');

        // Additional information
        Route::get('/votes', [VoteController::class, 'index'])->name('api.rooms.votes.index');
        Route::get('/votes/by-user', [VoteController::class, 'getVotesByUser'])->name('api.rooms.votes.byuser.get');
    });
});

