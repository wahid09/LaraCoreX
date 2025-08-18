<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    // Route::get('/home', function () {
    //     return view('home');
    // })->name('home');
    Route::get('/', function () {
    return view('dashboard');
    });
});
