<?php

use App\Http\Controllers\Teacher\AiAssistantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function (): void {
        Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai.index');
        Route::post('/ai-assistant', [AiAssistantController::class, 'generate'])
            ->middleware('throttle:6,1')
            ->name('ai.generate');
    });
