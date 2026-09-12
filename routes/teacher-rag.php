<?php

use App\Http\Controllers\Teacher\KnowledgeAssistantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.rag.')
    ->group(function (): void {
        Route::get('/knowledge-assistant', [KnowledgeAssistantController::class, 'index'])->name('index');
        Route::post('/knowledge-assistant/sources', [KnowledgeAssistantController::class, 'storeSource'])
            ->middleware('throttle:12,1')
            ->name('sources.store');
        Route::post('/knowledge-assistant/sources/{source}/reindex', [KnowledgeAssistantController::class, 'reindex'])
            ->middleware('throttle:12,1')
            ->name('sources.reindex');
        Route::delete('/knowledge-assistant/sources/{source}', [KnowledgeAssistantController::class, 'destroy'])
            ->name('sources.destroy');
        Route::post('/knowledge-assistant/courses/{course}/index', [KnowledgeAssistantController::class, 'indexCourse'])
            ->middleware('throttle:6,1')
            ->name('courses.index');
        Route::post('/knowledge-assistant/ask', [KnowledgeAssistantController::class, 'ask'])
            ->middleware('throttle:6,1')
            ->name('ask');
    });
