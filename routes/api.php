<?php

use App\Actions\Dashboard\GetDashboardSummaryAction;
use App\Actions\InventoryItem\CreateItem;
use App\Actions\InventoryItem\DeleteItem;
use App\Actions\InventoryItem\LoadItem;
use App\Actions\InventoryItem\LoadItems;
use App\Actions\InventoryItem\UpdateItem;
use App\Actions\ShoppingCategory\CreateShoppingCategoryAction;
use App\Actions\ShoppingCategory\DeleteShoppingCategoryAction;
use App\Actions\ShoppingCategory\GetShoppingCategoriesAction;
use App\Actions\ShoppingCategory\UpdateShoppingCategoryAction;
use App\Actions\ShoppingList\AddInventoryItemToShoppingListAction;
use App\Actions\ShoppingList\AddMultipleItemsToShoppingListAction;
use App\Actions\ShoppingList\AddStandaloneItemToShoppingListAction;
use App\Actions\ShoppingList\CreateShoppingListAction;
use App\Actions\ShoppingList\CreateShoppingListItemAction;
use App\Actions\ShoppingList\DeleteShoppingListAction;
use App\Actions\ShoppingList\DeleteShoppingListItemAction;
use App\Actions\ShoppingList\GetShoppingListAction;
use App\Actions\ShoppingList\GetShoppingListItemAction;
use App\Actions\ShoppingList\GetShoppingListItemsAction;
use App\Actions\ShoppingList\GetShoppingListsByUserAction;
use App\Actions\ShoppingList\UpdateShoppingListAction;
use App\Actions\ShoppingList\UpdateShoppingListItemAction;
use App\Actions\ShoppingList\UpdateShoppingListItemsBulkAction;
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

Route::get('/dashboard', GetDashboardSummaryAction::class)->middleware('auth:sanctum');

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

// Shopping List Routes
Route::get('/shopping-lists', GetShoppingListsByUserAction::class)->middleware('auth:sanctum');
Route::post('/shopping-lists', CreateShoppingListAction::class)->middleware('auth:sanctum');
Route::get('/shopping-lists/{shoppingList}', GetShoppingListAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::put('/shopping-lists/{shoppingList}', UpdateShoppingListAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::delete('/shopping-lists/{shoppingList}', DeleteShoppingListAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);

// Shopping List Items Routes
Route::get('/shopping-lists/{shoppingList}/items', GetShoppingListItemsAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::post('/shopping-lists/{shoppingList}/items', CreateShoppingListItemAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::post('/shopping-lists/{shoppingList}/items/bulk', AddMultipleItemsToShoppingListAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::put('/shopping-lists/{shoppingList}/items/bulk', UpdateShoppingListItemsBulkAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::get('/shopping-lists/{shoppingList}/items/{shoppingListItem}', GetShoppingListItemAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::put('/shopping-lists/{shoppingList}/items/{shoppingListItem}', UpdateShoppingListItemAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::delete('/shopping-lists/{shoppingList}/items/{shoppingListItem}', DeleteShoppingListItemAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::post('/shopping-lists/{shoppingList}/items/from-inventory', AddInventoryItemToShoppingListAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::post('/shopping-lists/{shoppingList}/items/standalone', AddStandaloneItemToShoppingListAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);

// Shopping Category Routes
Route::get('/shopping-categories', GetShoppingCategoriesAction::class)->middleware('auth:sanctum');
Route::post('/shopping-categories', CreateShoppingCategoryAction::class)->middleware('auth:sanctum');
Route::put('/shopping-categories/{shoppingCategory}', UpdateShoppingCategoryAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
Route::delete('/shopping-categories/{shoppingCategory}', DeleteShoppingCategoryAction::class)->middleware(['auth:sanctum', UserOwnsResource::class]);
