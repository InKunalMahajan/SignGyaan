<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'show'])
    ->name('dashboard');

Route::get('/dashboard/{role}', [DashboardController::class, 'show'])
    ->whereIn('role', ['learner', 'parents', 'teacher', 'admin', 'guest'])
    ->name('dashboard.role');
