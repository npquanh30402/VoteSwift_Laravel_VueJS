<?php

use App\Http\Controllers\Api\CandidateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::get('/rooms/{room}/candidates', [CandidateController::class, 'RoomCandidates'])->name('api.rooms.candidates.index');

    Route::post('/questions/{question}/candidate', [CandidateController::class, 'store'])->name('api.questions.candidate.store');

    Route::prefix('/candidate/{candidate}')->group(function () {
        Route::put('/', [CandidateController::class, 'update'])->name('api.candidate.update');
        Route::delete('/', [CandidateController::class, 'delete'])->name('api.candidate.delete');
    });
});

