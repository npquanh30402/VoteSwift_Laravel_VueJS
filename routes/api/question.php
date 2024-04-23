<?php

use App\Http\Controllers\Api\QuestionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::prefix('/rooms/{room}/questions')->group(function () {
        Route::get('/', [QuestionController::class, 'index'])->name('api.rooms.questions.index');
        Route::post('/', [QuestionController::class, 'store'])->name('api.rooms.question.store');
        Route::post('/csv', [QuestionController::class, 'importQuestionsFromCSV'])->name('api.rooms.questions.csv');
    });

    Route::prefix('/question/{question}')->group(function () {
        Route::put('/', [QuestionController::class, 'update'])->name('api.rooms.question.update');
        Route::delete('/', [QuestionController::class, 'delete'])->name('api.rooms.question.delete');
    });
});
