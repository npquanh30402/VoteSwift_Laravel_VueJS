<?php

// User search route
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/search', [UserController::class, 'search'])->name('user.search');

// Routes related to user chat
Route::prefix('/chat')->group(function () {
    Route::get('/{user}', [ChatController::class, 'index'])->name('api.user.chat.index');
    Route::post('/{user}', [ChatController::class, 'store'])->name('api.user.chat.store');
    Route::get('/', [ChatController::class, 'getUnreadAll'])->name('api.user.chat.unread.all');
    Route::post('/{user}/read', [ChatController::class, 'markRead'])->name('api.user.chat.read.all');
});

// Routes related to user friends
Route::prefix('/friends')->group(function () {
    Route::get('/', [FriendController::class, 'getFriends'])->name('api.user.friend.index');
    Route::post('/{friend}/unfriend', [FriendController::class, 'unfriend'])->name('api.user.unfriend');
});

// Routes related to friend requests
Route::prefix('/profile')->group(function () {
    Route::post('/{recipient}/send-friend-request', [FriendController::class, 'sendFriendRequest'])->name('api.user.send-friend-request');
    Route::post('/{sender}/accept-friend-request', [FriendController::class, 'acceptFriendRequest'])->name('api.user.accept-friend-request');
    Route::post('/{sender}/reject-friend-request', [FriendController::class, 'rejectFriendRequest'])->name('api.user.reject-friend-request');
    Route::post('/{recipient}/abort-request-sent', [FriendController::class, 'abortRequestSent'])->name('api.user.abort-request-sent');
});
