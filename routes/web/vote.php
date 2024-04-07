<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingRoomController;
use App\Http\Controllers\VotingRoomSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/voting', 'middleware' => 'auth'], function () {


    Route::prefix('room')->group(function () {
        // Voting Room routes
        Route::get('/{room}/dashboard', [VotingRoomController::class, 'dashboard'])->name('room.dashboard');
        Route::get('/create', [VotingRoomController::class, 'create'])->name('room.create');
        Route::post('/create', [VotingRoomController::class, 'store'])->name('room.store');
        Route::put('/{room}/update', [VotingRoomController::class, 'update'])->name('room.update');
        Route::delete('/{room}/delete', [VotingRoomController::class, 'delete'])->name('room.delete');
        Route::put('/{room}/publish', [VotingRoomController::class, 'publishRoom'])->name('room.publish');
        Route::get('/public', [VotingRoomController::class, 'showPublicRoom'])->name('public.room');

        // Invitation routes
        Route::get('/{room}/invitations/send', [InvitationController::class, 'sendInvitation'])->name('invitations.send');

        // Room settings routes
        Route::put('/{room}/settings/password/update', [VotingRoomSettingController::class, 'updatePasswordSetting'])->name('room.settings.password.update');
        Route::put('/{room}/settings/chat/update', [VotingRoomSettingController::class, 'updateChatSetting'])->name('room.settings.chat.update');

        Route::post('/{room}/question', [QuestionController::class, 'store'])->name('question.store');

        // Vote routes
        Route::get('/{room}/vote/password', [VoteController::class, 'passwordForm'])->name('vote.password.form');
        Route::post('/{room}/vote/password', [VoteController::class, 'passwordEntry'])->name('vote.password.entry');
        Route::get('/{room}/vote', [VoteController::class, 'main'])->name('vote.main')->middleware('prevent_voting_after_end');
        Route::post('/{room}/vote', [VoteController::class, 'store'])->name('vote.store');
        Route::get('/{room}/result', [VoteController::class, 'result'])->name('vote.result')->can('viewResults', 'room');
    });

    // Question routes
    Route::delete('/question/{question}', [QuestionController::class, 'delete'])->name('question.delete');
    Route::put('/question/{question}', [QuestionController::class, 'update'])->name('question.update');

    // Candidate routes
    Route::get('/question/{question}/candidate', [CandidateController::class, 'main'])->name('candidate.main');
    Route::post('/question/{question}/candidate', [CandidateController::class, 'store'])->name('candidate.store');
    Route::delete('/candidate/{candidate}', [CandidateController::class, 'delete'])->name('candidate.delete');
    Route::put('/candidate/{candidate}', [CandidateController::class, 'update'])->name('candidate.update');

    // User history route
    Route::get('/history', [VoteController::class, 'userHistory'])->name('user.history');
});
