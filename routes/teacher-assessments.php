<?php

use App\Http\Controllers\Teacher\AssessmentAnalyticsController;
use App\Http\Controllers\Teacher\AssessmentController;
use App\Http\Controllers\Teacher\AssessmentReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function (): void {
        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        Route::get('/assessments/{assessment}/preview', [AssessmentController::class, 'preview'])->name('assessments.preview');
        Route::patch('/assessments/{assessment}/publish', [AssessmentController::class, 'publish'])->name('assessments.publish');
        Route::patch('/assessments/{assessment}/archive', [AssessmentController::class, 'archive'])->name('assessments.archive');
        Route::get('/assessments/{assessment}/analytics', [AssessmentAnalyticsController::class, 'show'])->name('assessments.analytics');
        Route::get('/assessments/{assessment}/attempts', [AssessmentReviewController::class, 'index'])->name('assessments.attempts.index');
        Route::get('/assessment-attempts/{attempt}/review', [AssessmentReviewController::class, 'show'])->name('assessments.attempts.review');
        Route::patch('/assessment-attempts/{attempt}/review', [AssessmentReviewController::class, 'update'])->name('assessments.attempts.update');

        Route::post('/assessments/{assessment}/questions', [AssessmentController::class, 'storeQuestion'])->name('assessments.questions.store');
        Route::put('/assessments/{assessment}/questions/{question}', [AssessmentController::class, 'updateQuestion'])->name('assessments.questions.update');
        Route::delete('/assessments/{assessment}/questions/{question}', [AssessmentController::class, 'destroyQuestion'])->name('assessments.questions.destroy');
        Route::patch('/assessments/{assessment}/questions/{question}/move', [AssessmentController::class, 'moveQuestion'])->name('assessments.questions.move');
    });
