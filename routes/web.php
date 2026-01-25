<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Load API routes (simple include so we don't need RouteServiceProvider changes)
require __DIR__ . '/api.php';
