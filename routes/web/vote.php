<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingRoomController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/voting', 'middleware' => 'auth'], function () {
    // Room related-routes
    Route::get('/room/{room}', [VotingRoomController::class, 'main'])->name('room.main')->can('view', 'room');
    Route::get('/rooms', [VotingRoomController::class, 'create'])->name('room.create');
    Route::post('/rooms', [VotingRoomController::class, 'store'])->name('room.store');
    Route::put('/room/{room}', [VotingRoomController::class, 'update'])->name('room.update')->can('update', 'room');
    Route::delete('/room/{room}', [VotingRoomController::class, 'delete'])->name('room.delete')->can('delete', 'room');

    // Questions related-routes
    Route::get('/room/{room}/question', [QuestionController::class, 'main'])->name('question.main')->can('view', 'room');
    Route::post('/room/{room}/question', [QuestionController::class, 'store'])->name('question.store')->can('view', 'room');
    Route::delete('/question/{question}', [QuestionController::class, 'delete'])->name('question.delete')->can('delete', 'question');
    Route::put('/question/{question}', [QuestionController::class, 'update'])->name('question.update')->can('update', 'question');

    // Candidate related-routes
    Route::get('/question/{question}/candidate', [CandidateController::class, 'main'])->name('candidate.main')->can('view', 'question');
    Route::post('/question/{question}/candidate', [CandidateController::class, 'store'])->name('candidate.store')->can('view', 'question');
    Route::delete('/candidate/{candidate}', [CandidateController::class, 'delete'])->name('candidate.delete')->can('delete', 'candidate');
    Route::put('/candidate/{candidate}', [CandidateController::class, 'update'])->name('candidate.update')->can('update', 'candidate');

    //Votes related-routes
    Route::get('/room/{room}/vote/password', [VoteController::class, 'passwordForm'])->name('vote.password.form');
    Route::post('/room/{room}/vote/password', [VoteController::class, 'passwordEntry'])->name('vote.password.entry');
    Route::get('/room/{room}/vote', [VoteController::class, 'main'])->name('vote.main')->middleware('voting_room_password');
    Route::post('/room/{room}/vote', [VoteController::class, 'store'])->name('vote.store');
    Route::get('/room/{room}/vote/result', [VoteController::class, 'result'])->name('vote.result')->can('viewResults', 'room');

    Route::get('/history', [VoteController::class, 'userHistory'])->name('user.history');
});
