<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UnitController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('subjects', SubjectController::class)
            ->except('show');

        Route::resource('courses', CourseController::class)
            ->except('show');

        Route::resource('units', UnitController::class)
            ->except('show');

        Route::resource('lessons', LessonController::class)
            ->except('show');

        Route::view('/practice-resources', 'admin.placeholder', [
            'title' => 'Practice & Resources',
            'description' => 'Manage practice questions, downloadable resources and supporting learning material.',
        ])->name('practice.index');

        Route::view('/media', 'admin.placeholder', [
            'title' => 'Media',
            'description' => 'Organise learning images, ISL videos and other media used throughout SignGyaan.',
        ])->name('media.index');

        Route::view('/users', 'admin.placeholder', [
            'title' => 'Users',
            'description' => 'Review learner accounts, administrators and platform learning activity.',
        ])->name('users.index');

        Route::view('/settings', 'admin.placeholder', [
            'title' => 'Settings',
            'description' => 'Manage future platform preferences and administration settings for SignGyaan.',
        ])->name('settings.index');
    });
