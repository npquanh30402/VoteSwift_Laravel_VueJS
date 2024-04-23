<?php

use App\Http\Controllers\Api\ImageUploadController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], static function () {
    Route::post('/images', [ImageUploadController::class, 'store'])->name('api.images.store');
});

