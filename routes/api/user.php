<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::prefix('users')->group(function () {
        Route::get('/search', [UserController::class, 'searchUser'])->name('api.users.search');

//        Route::post('/music', [MusicController::class, 'store'])->name('api.users.music.store');
//        Route::delete('/music/{music}', [MusicController::class, 'delete'])->name('api.users.music.delete');
    });
});

