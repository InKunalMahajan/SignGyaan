<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\LearningProgressController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/my-courses', [LearningController::class, 'myCourses'])->name('my-courses');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('throttle:20,1')
        ->name('profile.update');
    Route::get('/accessibility', [ProfileController::class, 'accessibility'])
        ->name('profile.accessibility');
    Route::patch('/profile/accessibility', [ProfileController::class, 'updateAccessibility'])
        ->middleware('throttle:20,1')
        ->name('profile.accessibility.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->middleware('throttle:10,1')
        ->name('profile.password.update');

    Route::post('/learning-progress', [LearningProgressController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('learning-progress.store');

    Route::post('/learning-progress/video', [LearningProgressController::class, 'storeVideoProgress'])
        ->middleware('throttle:30,1')
        ->name('learning-progress.video.store');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
