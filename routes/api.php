<?php


use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ImageUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('api.image.upload');

Route::post('/images/{room}/upload', [FileUploadController::class, 'storeAttachment'])->name('api.room.attachment.store');
