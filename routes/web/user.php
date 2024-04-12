<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSeenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/user'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', [AuthController::class, 'getLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::get('/register', [AuthController::class, 'getRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    });

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::resource('notification', NotificationController::class)->only(['index']);

        Route::get('/dashboard', [UserController::class, 'getDashboard'])->name('dashboard.user');
        Route::get('/settings', [UserController::class, 'showSettings'])->name('user.settings');
        Route::put('/settings', [UserController::class, 'storeInformation'])->name('user.settings.update');

        Route::get('/music-settings', [UserController::class, 'showMusicPlayerSettings'])->name('user.music.settings');
        Route::post('/music-settings', [UserController::class, 'updateMusicPlayerSettings'])->name('user.music.settings.update');
        Route::post('/music-settings/upload', [MusicController::class, 'uploadMusic'])->name('user.music.settings.upload');
        Route::delete('/music-settings/{music}/delete', [MusicController::class, 'deleteMusic'])->name('user.music.settings.delete');

        Route::get('/profile/{user}', [UserController::class, 'profile'])->name('user.profile');

        Route::get('/friends', [FriendController::class, 'getFriends'])->name('user.friends');
        Route::post('/profile/{recipient}/send-friend-request', [FriendController::class, 'sendFriendRequest'])->name('user.send-friend-request');
        Route::post('/profile/{sender}/accept-friend-request', [FriendController::class, 'acceptFriendRequest'])
            ->name('user.accept-friend-request');

        Route::post('/{sender}/reject-friend-request/', [FriendController::class, 'rejectFriendRequest'])
            ->name('user.reject-friend-request');
        Route::post('/{recipient}/abort-request-sent/', [FriendController::class, 'abortRequestSent'])
            ->name('user.abort-request-sent');
        Route::post('/{friend}/unfriend', [FriendController::class, 'unfriend'])->name('user.unfriend');

//        Route::middleware(['check_friendship'])->group(function () {
//            Route::get('/chat', [ChatController::class, 'main'])->name('chat.main');
//            Route::post('/chat/message/{user}', [ChatController::class, 'messageReceived'])->name('chat.message');
//        });
        Route::get('/chat', [ChatController::class, 'main'])->name('chat.main');
        Route::post('/chat/message/{user}', [ChatController::class, 'messageReceived'])->name('chat.message');

    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [AuthController::class, 'getVerifyPage'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'sendEmailVerificationNotification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});
