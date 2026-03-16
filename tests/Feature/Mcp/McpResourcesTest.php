<?php

use App\Mcp\Resources\DashboardResource;
use App\Mcp\Resources\InventoryItemResource;
use App\Mcp\Resources\InventoryItemsResource;
use App\Mcp\Resources\ShoppingListResource;
use App\Mcp\Resources\ShoppingListsResource;
use App\Mcp\Resources\StockLocationsResource;
use App\Models\InventoryItem;
use App\Models\ShoppingList;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request as McpRequest;
use Laravel\Mcp\Response;

/**
 * Helper to call a resource's handle() with a Laravel\Mcp\Request and auth guard set up.
 *
 * Resources use Laravel\Mcp\Request, whose user() method resolves via the auth guard.
 * Query params and URI template variables (e.g. 'id') are merged into the request arguments.
 */
function callResource(object $resource, User $user, array $queryParams = [], array $extra = []): Response
{
    Auth::login($user);

    $request = new McpRequest(array_merge($queryParams, $extra));

    return app()->call([$resource, 'handle'], ['request' => $request]);
}

// ---------------------------------------------------------------------------
// DashboardResource
// ---------------------------------------------------------------------------

test('dashboard resource returns expected keys', function () {
    $user = User::factory()->create();

    $result = decodeResponse(callResource(app(DashboardResource::class), $user));

    expect($result)->toHaveKeys([
        'total_items',
        'total_locations',
        'active_shopping_lists',
        'low_stock_items',
        'expiring_items',
        'recent_items',
    ]);
});

test('dashboard resource counts only the authenticated user\'s items', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    InventoryItem::factory()->count(3)->create(['user_id' => $user->id]);
    InventoryItem::factory()->count(5)->create(['user_id' => $other->id]);

    $result = decodeResponse(callResource(app(DashboardResource::class), $user));

    expect($result['total_items'])->toBe(3);
});

test('dashboard resource surfaces low stock items', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 0, 'reorder_point' => 5]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 10, 'reorder_point' => 5]);

    $result = decodeResponse(callResource(app(DashboardResource::class), $user));

    expect($result['low_stock_items'])->toHaveCount(1);
});

// ---------------------------------------------------------------------------
// InventoryItemsResource
// ---------------------------------------------------------------------------

test('inventory items resource returns items for the authenticated user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    InventoryItem::factory()->count(3)->create(['user_id' => $user->id]);
    InventoryItem::factory()->count(2)->create(['user_id' => $other->id]);

    $result = decodeResponse(callResource(app(InventoryItemsResource::class), $user));

    expect($result['total'])->toBe(3);
    expect($result['items'])->toHaveCount(3);
});

test('inventory items resource supports search filter', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Olive Oil']);
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Salt']);

    $result = decodeResponse(callResource(app(InventoryItemsResource::class), $user, ['search' => 'Olive']));

    expect($result['total'])->toBe(1);
    expect($result['items'][0]['name'])->toBe('Olive Oil');
});

test('inventory items resource supports location filter', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'stock_location_id' => $location->id]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'stock_location_id' => null]);

    $result = decodeResponse(callResource(app(InventoryItemsResource::class), $user, ['stock_location_id' => $location->id]));

    expect($result['total'])->toBe(1);
});

// ---------------------------------------------------------------------------
// InventoryItemResource
// ---------------------------------------------------------------------------

test('inventory item resource returns full item detail', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Batteries AA']);

    $result = decodeResponse(callResource(app(InventoryItemResource::class), $user, extra: ['id' => $item->id]));

    expect($result['id'])->toBe($item->id);
    expect($result['name'])->toBe('Batteries AA');
});

test('inventory item resource throws 404 for another user\'s item', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $other->id]);

    expect(fn () => callResource(app(InventoryItemResource::class), $user, extra: ['id' => $item->id]))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ---------------------------------------------------------------------------
// StockLocationsResource
// ---------------------------------------------------------------------------

test('stock locations resource returns only the authenticated user\'s locations', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    StockLocation::factory()->count(2)->create(['user_id' => $user->id]);
    StockLocation::factory()->count(3)->create(['user_id' => $other->id]);

    $result = decodeResponse(callResource(app(StockLocationsResource::class), $user));

    expect($result['total'])->toBe(2);
});

// ---------------------------------------------------------------------------
// ShoppingListsResource
// ---------------------------------------------------------------------------

test('shopping lists resource returns only the authenticated user\'s lists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    ShoppingList::factory()->count(2)->create(['user_id' => $user->id]);
    ShoppingList::factory()->create(['user_id' => $other->id]);

    $result = decodeResponse(callResource(app(ShoppingListsResource::class), $user));

    expect($result['total'])->toBe(2);
    expect($result['lists'])->toHaveCount(2);
});

// ---------------------------------------------------------------------------
// ShoppingListResource
// ---------------------------------------------------------------------------

test('shopping list resource returns the list with items', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id, 'name' => 'Costco Run']);

    $result = decodeResponse(callResource(app(ShoppingListResource::class), $user, extra: ['id' => $list->id]));

    expect($result['name'])->toBe('Costco Run');
});

test('shopping list resource throws 404 for another user\'s list', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $other->id]);

    expect(fn () => callResource(app(ShoppingListResource::class), $user, extra: ['id' => $list->id]))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
