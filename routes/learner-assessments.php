<?php

use App\Http\Controllers\Learner\AssessmentAttemptController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'active', 'learner'])
    ->prefix('learner')
    ->name('learner.')
    ->group(function (): void {
        Route::get('/assessments', [AssessmentAttemptController::class, 'index'])->name('assessments.index');
        Route::post('/assessments/{assessment}/start', [AssessmentAttemptController::class, 'start'])->name('assessments.start');
        Route::get('/assessment-attempts/{attempt}', [AssessmentAttemptController::class, 'show'])->name('assessments.attempts.show');
        Route::put('/assessment-attempts/{attempt}/answers', [AssessmentAttemptController::class, 'save'])->name('assessments.attempts.save');
        Route::get('/assessment-attempts/{attempt}/review', [AssessmentAttemptController::class, 'review'])->name('assessments.attempts.review');
        Route::post('/assessment-attempts/{attempt}/submit', [AssessmentAttemptController::class, 'submit'])->name('assessments.attempts.submit');
        Route::get('/assessment-attempts/{attempt}/result', [AssessmentAttemptController::class, 'result'])->name('assessments.attempts.result');
    });
