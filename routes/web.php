<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Learner\ParentLinkRequestController;
use App\Http\Controllers\Learner\ProfileController;
use App\Http\Controllers\Parents\LearnerLinkController;
use App\Http\Controllers\Parents\ProfileController as ParentProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/dashboard/guest', [DashboardController::class, 'guest'])
    ->name('dashboard.guest');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard/{role}', [DashboardController::class, 'show'])
        ->whereIn('role', ['learner', 'parents', 'teacher', 'admin'])
        ->name('dashboard.role');

    Route::prefix('learner')->name('learner.')->middleware('learner')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.destroy');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::get('/parent-links', [ParentLinkRequestController::class, 'index'])->name('parent-links.index');
        Route::patch('/parent-links/{link}', [ParentLinkRequestController::class, 'respond'])->name('parent-links.respond');
        Route::delete('/parent-links/{link}', [ParentLinkRequestController::class, 'destroy'])->name('parent-links.destroy');
    });

    Route::prefix('parents')->name('parents.')->middleware('parents')->group(function () {
        Route::get('/profile', [ParentProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [ParentProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ParentProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::post('/learners', [LearnerLinkController::class, 'store'])->name('learners.store');
        Route::get('/learners/{link}', [LearnerLinkController::class, 'show'])->name('learners.show');
        Route::delete('/learners/{link}', [LearnerLinkController::class, 'destroy'])->name('learners.destroy');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/status', [UserManagementController::class, 'toggleStatus'])->name('users.status');
    });
});
