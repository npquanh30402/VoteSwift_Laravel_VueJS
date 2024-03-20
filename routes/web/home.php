<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('homepage');
Route::get('/public-room', [HomeController::class, 'showPublicRoom'])->name('public.room')->middleware('auth');
