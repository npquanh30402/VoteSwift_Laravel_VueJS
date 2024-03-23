<?php


use App\Http\Controllers\Api\ImageUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('api.image.upload');
