<?php

use Illuminate\Support\Facades\Route;

Route::view('/search', 'pages.search')
    ->name('search');
