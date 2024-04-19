<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingRoomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/voting', 'middleware' => 'auth'], function () {
    Route::prefix('room')->group(function () {
        // Voting Room routes
        Route::get('/{room}/dashboard', [VotingRoomController::class, 'dashboard'])->name('room.dashboard');
        Route::get('/create', [VotingRoomController::class, 'create'])->name('room.create');
        Route::put('/{room}/publish', [VotingRoomController::class, 'publishRoom'])->name('room.publish');
        Route::get('/public', [VotingRoomController::class, 'showPublicRoom'])->name('public.room');

        // InvitationMail routes
        Route::get('/{room}/invitations/send', [InvitationController::class, 'sendInvitation'])->name('invitations.send');

        // Vote routes
        Route::get('/{room}/vote/password/token/{token?}', [VoteController::class, 'passwordForm'])->name('vote.password.form');
        Route::post('/{room}/vote/password/token/{token?}', [VoteController::class, 'passwordEntry'])->name('vote.password.entry');
        Route::get('/{room}/vote', [VoteController::class, 'main'])->name('vote.main')->middleware(['voting_room_authorization', 'prevent_voting_time', 'prevent_duplicate_votes']);
    });

    // User history route
    Route::get('/history', [VoteController::class, 'userHistory'])->name('user.history');
});
