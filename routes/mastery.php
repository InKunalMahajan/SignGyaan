<?php

use App\Http\Controllers\Learner\ProgressController as LearnerProgressController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'active'])->group(function (): void {
    Route::prefix('learner')->name('learner.')->middleware('learner')->group(function (): void {
        Route::get('/progress', [LearnerProgressController::class, 'index'])->name('progress.index');
        Route::get('/progress/courses/{course}', [LearnerProgressController::class, 'show'])->name('progress.courses.show');
    });

    Route::prefix('teacher')->name('teacher.')->middleware('teacher')->group(function (): void {
        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        Route::get('/progress/learners/{learner}', [TeacherProgressController::class, 'show'])->name('progress.learners.show');
    });
});
