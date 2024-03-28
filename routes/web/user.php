<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationSeenController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => '/user'], function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', [AuthController::class, 'getLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::get('/register', [AuthController::class, 'getRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    });

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::resource('notification', NotificationController::class)->only(['index']);
        Route::put('notification/{notification}/mark-as-read', NotificationSeenController::class)->name('notification.read');

        Route::get('/dashboard', [UserController::class, 'getDashboard'])->name('dashboard.user');
        Route::get('/settings', [UserController::class, 'showSettings'])->name('user.settings');
        Route::put('/settings', [UserController::class, 'storeInformation'])->name('user.settings.update');

        Route::get('/music-settings', [UserController::class, 'showMusicPlayerSettings'])->name('user.music.settings');
        Route::post('/music-settings', [UserController::class, 'updateMusicPlayerSettings'])->name('user.music.settings.update');
        Route::post('/music-settings/upload', [UserController::class, 'uploadMusic'])->name('user.music.settings.upload');

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

        Route::middleware(['check_friendship'])->group(function () {
            Route::get('/chat/{user}', [ChatController::class, 'main'])->name('chat.main');
            Route::post('/chat/message/{user}', [ChatController::class, 'messageReceived'])->name('chat.message');
        });

    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', function () {
        return Inertia::render('Users/Auth/VerifyEmail');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard.user')->with('success', 'Email successfully verified!');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});
