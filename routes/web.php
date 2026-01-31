<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

// Forward all other routes to Vue router
Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');
