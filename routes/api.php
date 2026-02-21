<?php

use App\Actions\InventoryItem\CreateItem;
use App\Actions\InventoryItem\DeleteItem;
use App\Actions\InventoryItem\LoadItem;
use App\Actions\InventoryItem\LoadItems;
use App\Actions\InventoryItem\UpdateItem;
use App\Actions\StockLocation\CreateStockLocation;
use App\Actions\StockLocation\DeleteStockLocation;
use App\Actions\StockLocation\LoadStockLocation;
use App\Actions\StockLocation\LoadStockLocations;
use App\Actions\StockLocation\UpdateStockLocation;
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
Route::put('/inventory-items/{inventoryItem}', UpdateItem::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::delete('/inventory-items/{inventoryItem}', DeleteItem::class)->middleware(['auth:sanctum', UserOwnsResource::class]);

Route::get('/stock-locations', LoadStockLocations::class)->middleware('auth:sanctum');
Route::post('/stock-locations', CreateStockLocation::class)->middleware('auth:sanctum');
Route::get('/stock-locations/{stockLocation}', LoadStockLocation::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::put('/stock-locations/{stockLocation}', UpdateStockLocation::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::delete('/stock-locations/{stockLocation}', DeleteStockLocation::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
