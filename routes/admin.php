<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\AssessmentQuestionController;
use App\Http\Controllers\Admin\AssessmentResultController;
use App\Http\Controllers\Admin\CourseBuilderController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PracticeResourceController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VocabularyTermController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('subjects', SubjectController::class)
            ->except('show');

        Route::get('/courses/{course}/builder', CourseBuilderController::class)
            ->name('courses.builder');

        Route::resource('courses', CourseController::class)
            ->except('show');

        Route::resource('units', UnitController::class)
            ->except('show');

        Route::resource('lessons', LessonController::class)
            ->except('show');

        Route::resource('vocabulary', VocabularyTermController::class)
            ->parameters(['vocabulary' => 'vocabulary'])
            ->except('show');

        Route::get('/practice-resources', [PracticeResourceController::class, 'index'])->name('practice.index');
        Route::get('/practice-resources/create', [PracticeResourceController::class, 'create'])->name('practice.create');
        Route::post('/practice-resources', [PracticeResourceController::class, 'store'])->name('practice.store');
        Route::get('/practice-resources/{practiceResource}/edit', [PracticeResourceController::class, 'edit'])->name('practice.edit');
        Route::put('/practice-resources/{practiceResource}', [PracticeResourceController::class, 'update'])->name('practice.update');
        Route::delete('/practice-resources/{practiceResource}', [PracticeResourceController::class, 'destroy'])->name('practice.destroy');

        Route::resource('assessments', AssessmentController::class)
            ->except('show');

        Route::get('/assessments/{assessment}/questions', [AssessmentQuestionController::class, 'index'])
            ->name('assessments.questions.index');
        Route::get('/assessments/{assessment}/questions/create', [AssessmentQuestionController::class, 'create'])
            ->name('assessments.questions.create');
        Route::post('/assessments/{assessment}/questions', [AssessmentQuestionController::class, 'store'])
            ->name('assessments.questions.store');
        Route::get('/assessments/{assessment}/questions/{question}/edit', [AssessmentQuestionController::class, 'edit'])
            ->name('assessments.questions.edit');
        Route::put('/assessments/{assessment}/questions/{question}', [AssessmentQuestionController::class, 'update'])
            ->name('assessments.questions.update');
        Route::delete('/assessments/{assessment}/questions/{question}', [AssessmentQuestionController::class, 'destroy'])
            ->name('assessments.questions.destroy');

        Route::get('/assessment-results', [AssessmentResultController::class, 'index'])
            ->name('assessment-results.index');
        Route::get('/assessment-results/{attempt}', [AssessmentResultController::class, 'show'])
            ->whereNumber('attempt')
            ->name('assessment-results.show');

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
