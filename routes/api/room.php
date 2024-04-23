<?php

use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\InvitationMailController;
use App\Http\Controllers\Api\VotingRoomAttachmentController;
use App\Http\Controllers\Api\VotingRoomController;
use App\Http\Controllers\Api\VotingRoomSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/rooms', [VotingRoomController::class, 'index'])->name('api.rooms.index');

    Route::prefix('/room')->group(function () {
        Route::get('/{room}', [VotingRoomController::class, 'show'])->name('api.room.show');
        Route::post('/', [VotingRoomController::class, 'store'])->name('api.room.store');
        Route::put('/{room}', [VotingRoomController::class, 'update'])->name('api.room.update');
        Route::delete('/{room}', [VotingRoomController::class, 'delete'])->name('api.room.delete');
        Route::get('/{room}/duplicate', [VotingRoomController::class, 'duplicate'])->name('api.room.duplicate');
        Route::post('/{room}/publish', [VotingRoomController::class, 'publish'])->name('api.room.publish');

        // setting routes
        Route::get('/{room}/settings', [VotingRoomSettingController::class, 'index'])->name('api.room.settings.index');
        Route::put('/{room}/settings', [VotingRoomSettingController::class, 'update'])->name('api.room.settings.update');

        // attachment routes
        Route::get('/{room}/attachments', [VotingRoomAttachmentController::class, 'index'])->name('api.room.attachments.index');
        Route::post('/{room}/attachments', [VotingRoomAttachmentController::class, 'store'])->name('api.room.attachments.store');
        Route::delete('/attachments/{attachment}', [VotingRoomAttachmentController::class, 'delete'])->name('api.room.attachments.delete');

        // invitation routes
        Route::get('/{room}/invitations', [InvitationController::class, 'index'])->name('api.room.invitations.index');
        Route::post('/{room}/invitations', [InvitationController::class, 'store'])->name('api.room.invitations.store');
//        Route::post('/{room}/invitations/send', [InvitationController::class, 'sendInvitation'])->name('api.room.invitations.send');
        Route::post('/{room}/invitations/csv', [InvitationController::class, 'importInvitationsFromCSV'])->name('api.room.invitations.csv');

        // invitation mail routes
        Route::get('/{room}/invitations/mail', [InvitationMailController::class, 'index'])->name('api.room.invitations.mail.index');
        Route::post('/{room}/invitations/mail', [InvitationMailController::class, 'storeOrUpdate'])->name('api.room.invitation.mail.store');
        Route::delete('/invitations/mail/{invitationMail}', [InvitationMailController::class, 'delete'])->name('api.invitation.mail.delete');

        // feedback routes
        Route::post('/{room}/users/{user}/feedback', [FeedbackController::class, 'store'])->name('api.room.user.feedback.store');

        Route::get('/{room}/tie', [VotingRoomController::class, 'handleTieAndCreateNewVotingRound'])->name('api.rooms.tie.create');
    });
});
