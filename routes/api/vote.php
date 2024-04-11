<?php

use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\VotingChatController;
use Illuminate\Support\Facades\Route;

// Routes related to voting chat
Route::prefix('/voting/{room}/chat')->group(function () {
    Route::get('/', [VotingChatController::class, 'index'])->name('api.vote.chat.index');
    Route::post('/', [VotingChatController::class, 'store'])->name('api.vote.chat.store');
});

// Routes related to voting in a room
Route::prefix('/room/{room}')->group(function () {
    Route::get('/start', [VoteController::class, 'startVote'])->name('api.room.vote.start');
    Route::get('/results', [VoteController::class, 'getVoteResults'])->name('api.room.vote.results');
    Route::post('/choice', [VoteController::class, 'broadcastChoice'])->name('api.room.vote.broadcast.choice');
    Route::get('/join', [VoteController::class, 'getJoinTimes'])->name('api.room.vote.get.join.times');
    Route::post('/join', [VoteController::class, 'storeJoinTime'])->name('api.room.vote.store.join.time');
    Route::delete('/user/{user}/join', [VoteController::class, 'deleteJoinTime'])->name('api.room.vote.delete.join.time');
    Route::get('/choices', [VoteController::class, 'getUserChoices'])->name('api.room.vote.get.choices');
    Route::post('/choices', [VoteController::class, 'storeUserChoices'])->name('api.room.vote.store.choices');
    Route::delete('/user/{user}/choices', [VoteController::class, 'deleteUserChoices'])->name('api.room.vote.delete.choices');
});
