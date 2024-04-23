<?php

use App\Http\Controllers\Api\FriendController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::get('/friends', [FriendController::class, 'index'])->name('api.friends.index');

    Route::prefix('/friend')->group(function () {
        Route::post('/{sender}/unfriend/{receiver}', [FriendController::class, 'unfriend'])->name('api.friend.unfriend');
        Route::post('/{sender}/send-request-to/{receiver}', [FriendController::class, 'sendFriendRequest'])->name('api.friend.send-request');
        Route::post('/{sender}/accept-request-from/{receiver}', [FriendController::class, 'acceptFriendRequest'])->name('api.friend.accept-request');
        Route::post('/{sender}/reject-request-from/{receiver}', [FriendController::class, 'rejectFriendRequest'])->name('api.friend.reject-request');
        Route::post('/{sender}/abort-request-to/{receiver}', [FriendController::class, 'abortRequestSent'])->name('api.friend.abort-request');
    });
});
