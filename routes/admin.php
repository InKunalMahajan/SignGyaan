<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PracticeResourceController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
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

        Route::get('/practice-resources', [PracticeResourceController::class, 'index'])->name('practice.index');
        Route::get('/practice-resources/create', [PracticeResourceController::class, 'create'])->name('practice.create');
        Route::post('/practice-resources', [PracticeResourceController::class, 'store'])->name('practice.store');
        Route::get('/practice-resources/{practiceResource}/edit', [PracticeResourceController::class, 'edit'])->name('practice.edit');
        Route::put('/practice-resources/{practiceResource}', [PracticeResourceController::class, 'update'])->name('practice.update');
        Route::delete('/practice-resources/{practiceResource}', [PracticeResourceController::class, 'destroy'])->name('practice.destroy');

        Route::resource('media', MediaController::class)
            ->parameters(['media' => 'mediaAsset'])
            ->except('show');

        Route::resource('users', UserController::class)
            ->only(['index', 'edit', 'update', 'destroy']);

        Route::view('/settings', 'admin.placeholder', [
            'title' => 'Settings',
            'description' => 'Manage future platform preferences and administration settings for SignGyaan.',
        ])->name('settings.index');
    });
