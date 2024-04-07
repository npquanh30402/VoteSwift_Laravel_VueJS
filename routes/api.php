<?php


use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\VotingChatController;
use App\Http\Controllers\Api\VotingRoomController;
use App\Http\Controllers\Api\VotingRoomSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/images/{room}/upload', [FileUploadController::class, 'storeAttachment'])->name('api.room.attachment.store');

Route::group(['middleware' => 'web'], function () {
    Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('api.image.upload');

    Route::get('/room/', [VotingRoomController::class, 'index'])->name('api.room.index');
    Route::post('/room/', [VotingRoomController::class, 'store'])->name('api.room.store');
    Route::delete('/room/{room}', [VotingRoomController::class, 'delete'])->name('api.room.destroy');

    Route::get('/search', [UserController::class, 'search'])->name('user.search');

    Route::get('/room/{room}/invitations', [InvitationController::class, 'getInvitations'])->name('api.room.invitation.index');
    Route::post('/room/{room}/invitations', [InvitationController::class, 'store'])->name('api.room.invitation.store');

    Route::get('/room/{room}/settings/invitations', [VotingRoomSettingController::class, 'getSettings'])->name('api.room.setting.invitation.index');
    Route::put('/room/{room}/settings/invitations', [VotingRoomSettingController::class, 'updateInvitationSetting'])->name('api.room.setting.invitation.update');


    Route::get('/chat/{user}', [ChatController::class, 'index'])->name('api.user.chat.index');
    Route::post('/chat/{user}', [ChatController::class, 'store'])->name('api.user.chat.store');
    Route::get('/chat/', [ChatController::class, 'getUnreadAll'])->name('api.user.chat.unread.all');
    Route::post('/chat/{user}/read', [ChatController::class, 'markRead'])->name('api.user.chat.read.all');

    Route::get('/room/{room}/questions', [QuestionController::class, 'index'])->name('api.room.question.index');
    Route::post('/room/{room}/questions', [QuestionController::class, 'store'])->name('api.room.question.store');
    Route::put('/room/questions/{question}', [QuestionController::class, 'update'])->name('api.room.question.update');
    Route::delete('/question/{question}', [QuestionController::class, 'delete'])->name('api.room.question.destroy');

    Route::get('/question/{question}/candidates', [CandidateController::class, 'QuestionCandidates'])->name('api.question.candidate.index');
    Route::get('/room/{room}/candidates', [CandidateController::class, 'RoomCandidates'])->name('api.room.candidate.index');
    Route::post('/question/{question}/candidates', [CandidateController::class, 'store'])->name('api.question.candidate.store');
    Route::put('/candidate/{candidate}', [CandidateController::class, 'update'])->name('api.candidate.update');
    Route::delete('/candidate/{candidate}', [CandidateController::class, 'destroy'])->name('api.candidate.destroy');

    Route::get('/voting/{room}/chat/', [VotingChatController::class, 'index'])->name('api.vote.chat.index');
    Route::post('/voting/{room}/chat/', [VotingChatController::class, 'store'])->name('api.vote.chat.store');

    Route::get('/room/{room}/start', [VoteController::class, 'startVote'])->name('api.room.vote.start');
    Route::get('/room/{room}/results', [VoteController::class, 'getVoteResults'])->name('api.room.vote.results');
    Route::post('/room/{room}/choice', [VoteController::class, 'broadcastChoice'])->name('api.room.vote.broadcast.choice');
});
