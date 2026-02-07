<?php

use App\Actions\InventoryItem\CreateItem;
use App\Actions\InventoryItem\DeleteItem;
use App\Actions\InventoryItem\LoadItem;
use App\Actions\InventoryItem\LoadItems;
use App\Actions\Token\CreateToken;
use App\Actions\Token\DestroyToken;
use App\Actions\Token\LoadTokens;
use App\Http\Middleware\UserOwnsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/tokens', LoadTokens::class)->middleware('auth:sanctum');
Route::post('/tokens', CreateToken::class)->middleware('auth:sanctum');
Route::delete('/tokens/{tokenId}', DestroyToken::class)->middleware('auth:sanctum');

Route::get('/inventory-items', LoadItems::class)->middleware('auth:sanctum');
Route::post('/inventory-items', CreateItem::class)->middleware('auth:sanctum');
Route::get('/inventory-items/{inventoryItem}', LoadItem::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::delete('/inventory-items/{inventoryItem}', DeleteItem::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
