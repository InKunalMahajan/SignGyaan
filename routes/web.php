<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')
    ->name('home');


Route::view('/learn', 'pages.learn')
    ->name('learn');


Route::view('/subjects', 'pages.subjects')
    ->name('subjects');


Route::view('/explore', 'pages.explore')
    ->name('explore');


Route::view('/about', 'pages.about')
    ->name('about');