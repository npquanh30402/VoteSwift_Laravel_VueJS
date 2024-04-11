<?php

use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\VotingRoomAttachmentController;
use App\Http\Controllers\Api\VotingRoomController;
use App\Http\Controllers\Api\VotingRoomSettingController;
use Illuminate\Support\Facades\Route;

// Routes related to voting rooms
Route::prefix('/rooms')->group(function () {
    Route::get('/', [VotingRoomController::class, 'index'])->name('api.rooms.index');
    Route::post('/', [VotingRoomController::class, 'store'])->name('api.rooms.store');
    Route::put('/{room}', [VotingRoomController::class, 'update'])->name('api.rooms.update');
    Route::delete('/{room}', [VotingRoomController::class, 'destroy'])->name('api.rooms.destroy');

    // Attachments related to a specific voting room
    Route::prefix('/{room}/attachments')->group(function () {
        Route::get('/', [VotingRoomAttachmentController::class, 'index'])->name('api.attachments.index');
        Route::delete('/{attachment}', [VotingRoomAttachmentController::class, 'destroy'])->name('api.attachments.destroy');
    });

    // Questions related to a specific voting room
    Route::prefix('/{room}/questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('api.questions.index');
        Route::post('/', [QuestionController::class, 'store'])->name('api.questions.store');
        Route::put('/{question}', [QuestionController::class, 'update'])->name('api.questions.update');
        Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('api.questions.destroy');
        Route::get('/{question}/candidates', [CandidateController::class, 'QuestionCandidates'])->name('api.question.candidates.index');
    });

    // Invitations related to a specific voting room
    Route::prefix('/{room}/invitations')->group(function () {
        Route::get('/', [InvitationController::class, 'getInvitations'])->name('api.invitations.index');
        Route::post('/', [InvitationController::class, 'store'])->name('api.invitations.store');
    });

    // Settings related to a specific voting room
    Route::prefix('/{room}/settings')->group(function () {
        Route::get('/', [VotingRoomSettingController::class, 'getSettings'])->name('api.settings.index');
        Route::put('/', [VotingRoomSettingController::class, 'updateSettings'])->name('api.settings.update');
    });

    // Candidates related to a specific voting room
    Route::prefix('/{room}/candidates')->group(function () {
        Route::get('/', [CandidateController::class, 'RoomCandidates'])->name('api.candidates.index');
        Route::post('/', [CandidateController::class, 'store'])->name('api.candidates.store');
    });
});

// Routes related to individual questions
Route::prefix('/questions')->group(function () {
    Route::put('/{question}', [QuestionController::class, 'update'])->name('api.question.update');
    Route::delete('/{question}', [QuestionController::class, 'destroy'])->name('api.question.destroy');
});

// Routes related to individual candidates
Route::prefix('/candidates')->group(function () {
    Route::put('/{candidate}', [CandidateController::class, 'update'])->name('api.candidate.update');
    Route::delete('/{candidate}', [CandidateController::class, 'destroy'])->name('api.candidate.destroy');
});

// Route for deleting attachments
Route::delete('/attachments/{attachment}', [VotingRoomAttachmentController::class, 'destroy'])->name('api.attachment.destroy');
