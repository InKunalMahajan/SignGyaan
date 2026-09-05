<?php

use App\Http\Controllers\PublicCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicCatalogController::class, 'home'])
    ->name('home');

Route::get('/learn', [PublicCatalogController::class, 'learn'])
    ->name('learn');

Route::get('/subjects', [PublicCatalogController::class, 'subjects'])
    ->name('subjects');

Route::get('/subjects/{subject}', [PublicCatalogController::class, 'subject'])
    ->name('subjects.show');

Route::get('/subjects/{subject}/courses/{course}', [PublicCatalogController::class, 'course'])
    ->name('courses.show');

Route::get('/explore', [PublicCatalogController::class, 'explore'])
    ->name('explore');

Route::view('/about', 'pages.about')
    ->name('about');
