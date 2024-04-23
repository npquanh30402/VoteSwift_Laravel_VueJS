<?php

use App\Http\Controllers\Api\UserMessageController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::prefix('/chats')->group(function () {
        Route::get('/senders/{sender}/receivers/{receiver}', [UserMessageController::class, 'index'])->name('api.chats.index');

        Route::get('/users/{user}/unread', [UserMessageController::class, 'fetchUnreadMessages'])->name('api.chats.unread');
    });

    Route::prefix('/chat')->group(function () {
        Route::post('/senders/{sender}/receivers/{receiver}', [UserMessageController::class, 'store'])->name('api.chat.store');

        Route::post('/senders/{sender}/receivers/{receiver}/mark-read', [UserMessageController::class, 'markAsRead'])->name('api.chat.mark-read');
    });
});

