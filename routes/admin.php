<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('subjects', SubjectController::class)
            ->except('show');

        Route::view('/courses', 'admin.placeholder', [
            'title' => 'Courses',
            'description' => 'Manage courses inside each subject, including learning level, publishing and course structure.',
        ])->name('courses.index');

        Route::view('/units', 'admin.placeholder', [
            'title' => 'Units',
            'description' => 'Organise courses into ordered units that guide learners through a clear learning sequence.',
        ])->name('units.index');

        Route::view('/lessons', 'admin.placeholder', [
            'title' => 'Lessons',
            'description' => 'Manage lesson content, ISL video, notes, examples, practice and lesson ordering.',
        ])->name('lessons.index');

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
