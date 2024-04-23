<?php

use App\Http\Controllers\Api\VotingChatController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::prefix('/rooms/{room}/chats')->group(function () {
        Route::get('/', [VotingChatController::class, 'index'])->name('api.rooms.chats.index');
        Route::post('/', [VotingChatController::class, 'store'])->name('api.rooms.chat.store');
    });
});
