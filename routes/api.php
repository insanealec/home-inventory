<?php

use App\Actions\Dashboard\GetDashboardSummaryAction;
use App\Actions\Notification\DismissNotificationAction;
use App\Actions\Notification\GetNotificationPreferencesAction;
use App\Actions\Notification\GetNotificationsAction;
use App\Actions\Notification\MarkNotificationReadAction;
use App\Actions\Notification\UpdateNotificationPreferencesAction;
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

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard', GetDashboardSummaryAction::class);

    // Tokens — additional throttle:tokens on write operations
    Route::get('/tokens', LoadTokens::class);
    Route::post('/tokens', CreateToken::class)->middleware('throttle:tokens');
    Route::delete('/tokens/{tokenId}', DestroyToken::class)->middleware('throttle:tokens');

    // Inventory Items
    Route::get('/inventory-items', LoadItems::class);
    Route::post('/inventory-items', CreateItem::class);
    Route::get('/inventory-items/{inventoryItem}', LoadItem::class)->middleware(UserOwnsResource::class);
    Route::put('/inventory-items/{inventoryItem}', UpdateItem::class)->middleware(UserOwnsResource::class);
    Route::delete('/inventory-items/{inventoryItem}', DeleteItem::class)->middleware(UserOwnsResource::class);

    // Stock Locations
    Route::get('/stock-locations', LoadStockLocations::class);
    Route::post('/stock-locations', CreateStockLocation::class);
    Route::get('/stock-locations/{stockLocation}', LoadStockLocation::class)->middleware(UserOwnsResource::class);
    Route::put('/stock-locations/{stockLocation}', UpdateStockLocation::class)->middleware(UserOwnsResource::class);
    Route::delete('/stock-locations/{stockLocation}', DeleteStockLocation::class)->middleware(UserOwnsResource::class);

    // Shopping Lists
    Route::get('/shopping-lists', GetShoppingListsByUserAction::class);
    Route::post('/shopping-lists', CreateShoppingListAction::class);
    Route::get('/shopping-lists/{shoppingList}', GetShoppingListAction::class)->middleware(UserOwnsResource::class);
    Route::put('/shopping-lists/{shoppingList}', UpdateShoppingListAction::class)->middleware(UserOwnsResource::class);
    Route::delete('/shopping-lists/{shoppingList}', DeleteShoppingListAction::class)->middleware(UserOwnsResource::class);

    // Shopping List Items
    Route::middleware(UserOwnsResource::class)->group(function () {
        Route::get('/shopping-lists/{shoppingList}/items', GetShoppingListItemsAction::class);
        Route::post('/shopping-lists/{shoppingList}/items', CreateShoppingListItemAction::class);
        Route::post('/shopping-lists/{shoppingList}/items/bulk', AddMultipleItemsToShoppingListAction::class);
        Route::put('/shopping-lists/{shoppingList}/items/bulk', UpdateShoppingListItemsBulkAction::class);
        Route::get('/shopping-lists/{shoppingList}/items/{shoppingListItem}', GetShoppingListItemAction::class);
        Route::put('/shopping-lists/{shoppingList}/items/{shoppingListItem}', UpdateShoppingListItemAction::class);
        Route::delete('/shopping-lists/{shoppingList}/items/{shoppingListItem}', DeleteShoppingListItemAction::class);
        Route::post('/shopping-lists/{shoppingList}/items/from-inventory', AddInventoryItemToShoppingListAction::class);
        Route::post('/shopping-lists/{shoppingList}/items/standalone', AddStandaloneItemToShoppingListAction::class);
    });

    // Shopping Categories
    Route::get('/shopping-categories', GetShoppingCategoriesAction::class);
    Route::post('/shopping-categories', CreateShoppingCategoryAction::class);
    Route::put('/shopping-categories/{shoppingCategory}', UpdateShoppingCategoryAction::class)->middleware(UserOwnsResource::class);
    Route::delete('/shopping-categories/{shoppingCategory}', DeleteShoppingCategoryAction::class)->middleware(UserOwnsResource::class);

    // Notification Preferences
    Route::get('/user/notification-preferences', GetNotificationPreferencesAction::class);
    Route::put('/user/notification-preferences', UpdateNotificationPreferencesAction::class);

    // Notification Centre
    Route::get('/notifications', GetNotificationsAction::class);
    Route::put('/notifications/{notificationId}', MarkNotificationReadAction::class);
    Route::delete('/notifications/{notificationId}', DismissNotificationAction::class);
});
