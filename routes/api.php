<?php

use App\Actions\Token\CreateToken;
use App\Actions\Token\DestroyToken;
use App\Actions\Token\LoadTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tokens', LoadTokens::class)->middleware('auth:sanctum');
Route::post('/tokens', CreateToken::class)->middleware('auth:sanctum');
Route::delete('/tokens/{tokenId}', DestroyToken::class)->middleware('auth:sanctum');
