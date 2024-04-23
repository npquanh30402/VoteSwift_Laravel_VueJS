<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserMessageController;
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
        Route::put('/settings', [UserController::class, 'storeInformation'])->name('user.settings.update');
        
        Route::post('/music-settings', [UserController::class, 'updateMusicPlayerSettings'])->name('user.music.settings.update');
        Route::post('/music-settings/upload', [MusicController::class, 'uploadMusic'])->name('user.music.settings.upload');
        Route::delete('/music-settings/{music}/delete', [MusicController::class, 'deleteMusic'])->name('user.music.settings.delete');

        Route::get('/profile/{user}', [UserController::class, 'profile'])->name('user.profile');

//        Route::middleware(['check_friendship'])->group(function () {
//            Route::get('/chat', [ChatController::class, 'main'])->name('chat.main');
//            Route::post('/chat/message/{user}', [ChatController::class, 'messageReceived'])->name('chat.message');
//        });
        Route::get('/chat', [UserMessageController::class, 'index'])->name('chat.index');

    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [AuthController::class, 'getVerifyPage'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', [AuthController::class, 'sendEmailVerificationNotification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    Route::middleware('guest')->group(function () {
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->middleware('guest')->name('password.request');

        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('guest')->name('password.email');

        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->middleware('guest')->name('password.reset');

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');
    });
});

