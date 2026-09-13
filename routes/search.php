<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])
    ->get('/search', SearchController::class)
    ->name('search.index');
