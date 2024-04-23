<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationSeenController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::prefix('/notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('api.notifications.index');
        Route::get('/users/{user}/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread.count');
        Route::put('/users/{user}/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark.all.as.read');
    });

    Route::put('/notification/{notification}/mark-as-read', NotificationSeenController::class)->name('api.notification.mark.as.read');
});
