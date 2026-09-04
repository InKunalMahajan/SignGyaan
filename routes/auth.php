<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LearningProgressController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [LearningController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-learning', [LearningController::class, 'index'])->name('my-learning');

    Route::post('/learning-progress', [LearningProgressController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('learning-progress.store');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
