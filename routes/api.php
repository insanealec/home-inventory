<?php

use App\Actions\Token\LoadUserTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tokens', LoadUserTokens::class)->middleware('auth:sanctum');