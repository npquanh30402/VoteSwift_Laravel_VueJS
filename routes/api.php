<?php


use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('api.image.upload');

Route::post('/images/{room}/upload', [FileUploadController::class, 'storeAttachment'])->name('api.room.attachment.store');

Route::get('/search', [UserController::class, 'search'])->name('user.search');

Route::get('/room/{room}/invitations', [InvitationController::class, 'getInvitations'])->name('invitation.get');

Route::get('/room/{room}/start', [VoteController::class, 'startVote'])->name('api.room.vote.start')->middleware('web');

Route::group(['middleware' => 'web'], function () {
    Route::get('/question/{question}/candidates', [CandidateController::class, 'QuestionCandidates'])->name('api.question.candidate.index');
    Route::get('/room/{room}/candidates', [CandidateController::class, 'RoomCandidates'])->name('api.room.candidate.index');
    Route::post('/question/{question}/candidates', [CandidateController::class, 'store'])->name('api.question.candidate.store');
    Route::put('/candidate/{candidate}', [CandidateController::class, 'update'])->name('api.candidate.update');
    Route::delete('/candidate/{candidate}', [CandidateController::class, 'destroy'])->name('api.candidate.destroy');
});
