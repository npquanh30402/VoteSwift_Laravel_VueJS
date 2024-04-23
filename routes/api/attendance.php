<?php

use App\Http\Controllers\Api\UserAttendanceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::get('/rooms/{room}/attendance', [UserAttendanceController::class, 'index'])->name('api.rooms.attendance.index');

    Route::post('/rooms/{room}/attendance/join', [UserAttendanceController::class, 'storeJoinTime'])->name('api.rooms.attendance.join');
    Route::post('/rooms/{room}/attendance/leave', [UserAttendanceController::class, 'storeLeaveTime'])->name('api.rooms.attendance.leave');
});

