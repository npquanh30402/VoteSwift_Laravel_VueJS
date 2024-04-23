<?php

use App\Http\Controllers\Api\VoteController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/user.php';
require __DIR__ . '/api/user-chat.php';
require __DIR__ . '/api/friend.php';
require __DIR__ . '/api/notification.php';

require __DIR__ . '/api/room.php';
require __DIR__ . '/api/question.php';
require __DIR__ . '/api/candidate.php';
require __DIR__ . '/api/attendance.php';

require __DIR__ . '/api/vote-chat.php';

require __DIR__ . '/api/etc.php';

Route::group(['middleware' => 'web'], function () {
    Route::get('/room/{room}/start', [VoteController::class, 'startVote'])->name('api.room.vote.start');
    Route::get('/room/{room}/results', [VoteController::class, 'getVoteResults'])->name('api.room.vote.results');
    Route::post('/room/{room}/choice', [VoteController::class, 'broadcastChoice'])->name('api.room.vote.broadcast.choice');
    Route::post('/room/{room}/vote', [VoteController::class, 'storeVotes'])->name('api.room.vote.store');

    Route::get('/room/{room}/choices', [VoteController::class, 'getUserChoices'])->name('api.room.vote.get.choices');
    Route::post('/room/{room}/choices', [VoteController::class, 'storeUserChoices'])->name('api.room.vote.store.choices');
    Route::delete('/room/{room}/user/{user}/choices', [VoteController::class, 'deleteUserChoices'])->name('api.room.vote.delete.choices');

    Route::get('/room/{room}/votes', [VoteController::class, 'getVotes'])->name('api.room.votes.get');
    Route::get('/room/{room}/result-page-votes', [VoteController::class, 'getResultPageVotes'])->name('api.room.vote.results.get');
});
