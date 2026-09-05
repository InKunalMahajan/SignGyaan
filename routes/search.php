<?php

use App\Http\Controllers\PublicSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/search', PublicSearchController::class)
    ->name('search');
