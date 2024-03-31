<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingRoomController;
use App\Http\Controllers\VotingRoomSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/voting', 'middleware' => 'auth'], function () {

    // Voting Room routes
    Route::get('/room/{room}/dashboard', [VotingRoomController::class, 'dashboard'])->name('room.dashboard');

    Route::prefix('room')->group(function () {
        Route::get('/create', [VotingRoomController::class, 'create'])->name('room.create');
        Route::post('/create', [VotingRoomController::class, 'store'])->name('room.store');
        Route::put('/{room}/update', [VotingRoomController::class, 'update'])->name('room.update');
        Route::delete('/{room}/delete', [VotingRoomController::class, 'delete'])->name('room.delete');
        Route::put('/{room}/publish', [VotingRoomController::class, 'publishRoom'])->name('room.publish');
        Route::get('/public', [VotingRoomController::class, 'showPublicRoom'])->name('public.room');

        // Invitation routes
        Route::get('/{room}/invitations/send', [InvitationController::class, 'sendInvitation'])->name('invitations.send');
        Route::post('/{room}/invitations', [InvitationController::class, 'store'])->name('invitation.store');
        Route::delete('/invitations', [InvitationController::class, 'delete'])->name('invitation.delete');

        // Room settings routes
        Route::put('/{room}/settings/invitations/update', [VotingRoomSettingController::class, 'updateInvitationSetting'])->name('room.settings.invitation.update');
    });

    // Question routes
    Route::post('/room/{room}/question', [QuestionController::class, 'store'])->name('question.store');
    Route::delete('/question/{question}', [QuestionController::class, 'delete'])->name('question.delete');
    Route::put('/question/{question}', [QuestionController::class, 'update'])->name('question.update');

    // Candidate routes
    Route::get('/question/{question}/candidate', [CandidateController::class, 'main'])->name('candidate.main');
    Route::post('/question/{question}/candidate', [CandidateController::class, 'store'])->name('candidate.store');
    Route::delete('/candidate/{candidate}', [CandidateController::class, 'delete'])->name('candidate.delete');
    Route::put('/candidate/{candidate}', [CandidateController::class, 'update'])->name('candidate.update');

    // Vote routes
    Route::get('/room/{room}/vote/password', [VoteController::class, 'passwordForm'])->name('vote.password.form');
    Route::post('/room/{room}/vote/password', [VoteController::class, 'passwordEntry'])->name('vote.password.entry');
    Route::get('/room/{room}/vote', [VoteController::class, 'main'])->name('vote.main');
    Route::post('/room/{room}/vote', [VoteController::class, 'store'])->name('vote.store');
    Route::get('/room/{room}/result', [VoteController::class, 'result'])->name('vote.result')->can('viewResults', 'room');

    // User history route
    Route::get('/history', [VoteController::class, 'userHistory'])->name('user.history');
});
