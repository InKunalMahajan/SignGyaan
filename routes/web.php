<?php

use App\Http\Controllers\PublicAssessmentController;
use App\Http\Controllers\PublicCatalogController;
use App\Http\Controllers\PublicVocabularyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicCatalogController::class, 'home'])
    ->name('home');

Route::get('/learn', [PublicCatalogController::class, 'learn'])
    ->name('learn');

Route::get('/subjects', [PublicCatalogController::class, 'subjects'])
    ->name('subjects');

Route::get('/subjects/{subject}', [PublicCatalogController::class, 'subject'])
    ->where('subject', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('subjects.show');

Route::get('/subjects/{subject}/courses/{course}', [PublicCatalogController::class, 'course'])
    ->where([
        'subject' => '[a-z0-9]+(?:-[a-z0-9]+)*',
        'course' => '[a-z0-9]+(?:-[a-z0-9]+)*',
    ])
    ->name('courses.show');

Route::get('/isl-vocabulary', [PublicVocabularyController::class, 'index'])
    ->name('vocabulary.index');

Route::get('/isl-vocabulary/{vocabularyTerm}', [PublicVocabularyController::class, 'show'])
    ->where('vocabularyTerm', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('vocabulary.show');

Route::get('/assessments/{assessment}', [PublicAssessmentController::class, 'show'])
    ->whereNumber('assessment')
    ->name('assessments.show');

Route::middleware('auth')->group(function () {
    Route::post('/assessments/{assessment}/start', [PublicAssessmentController::class, 'start'])
        ->whereNumber('assessment')
        ->middleware('throttle:20,1')
        ->name('assessments.start');

    Route::get('/assessments/{assessment}/attempts/{attempt}', [PublicAssessmentController::class, 'play'])
        ->whereNumber('assessment')
        ->name('assessment-attempts.show');

    Route::get('/assessments/{assessment}/attempts/{attempt}/result', [PublicAssessmentController::class, 'result'])
        ->whereNumber('assessment')
        ->name('assessment-attempts.result');

    Route::post('/assessments/{assessment}/attempts/{attempt}/save', [PublicAssessmentController::class, 'save'])
        ->whereNumber('assessment')
        ->middleware('throttle:60,1')
        ->name('assessment-attempts.save');

    Route::post('/assessments/{assessment}/attempts/{attempt}/submit', [PublicAssessmentController::class, 'submit'])
        ->whereNumber('assessment')
        ->middleware('throttle:20,1')
        ->name('assessment-attempts.submit');
});

Route::get('/explore', [PublicCatalogController::class, 'explore'])
    ->name('explore');

Route::view('/about', 'pages.about')
    ->name('about');
