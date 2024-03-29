<?php


use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('api.image.upload');

Route::post('/images/{room}/upload', [FileUploadController::class, 'storeAttachment'])->name('api.room.attachment.store');

Route::get('/search', [UserController::class, 'search'])->name('user.search');

Route::get('/room/{room}/invitations', [InvitationController::class, 'getInvitations'])->name('invitation.get');
