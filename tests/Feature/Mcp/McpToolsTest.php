<?php

use App\Mcp\Tools\AddInventoryItemTool;
use App\Mcp\Tools\AddItemToShoppingListTool;
use App\Mcp\Tools\AdjustItemQuantityTool;
use App\Mcp\Tools\CreateShoppingListTool;
use App\Mcp\Tools\FindLowStockItemsTool;
use App\Mcp\Tools\GetDashboardTool;
use App\Mcp\Tools\GetInventoryItemsTool;
use App\Mcp\Tools\GetInventoryItemTool;
use App\Mcp\Tools\GetShoppingListsTool;
use App\Mcp\Tools\GetShoppingListTool;
use App\Mcp\Tools\GetStockLocationsTool;
use App\Mcp\Tools\MarkShoppingListCompleteTool;
use App\Mcp\Tools\UpdateInventoryItemTool;
use App\Models\InventoryItem;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request as McpRequest;

/**
 * Helper to call a tool's handle() with a Laravel\Mcp\Request and auth guard set up.
 *
 * Tools use Laravel\Mcp\Request, whose user() method resolves via the auth guard rather than
 * a request-level user resolver. Auth::login() sets up the guard so $request->user() works.
 */
function callTool(object $tool, User $user, array $data = []): \Laravel\Mcp\Response
{
    Auth::login($user);

    $request = new McpRequest($data);

    return app()->call([$tool, 'handle'], ['request' => $request]);
}

// ---------------------------------------------------------------------------
// AddInventoryItemTool
// ---------------------------------------------------------------------------

test('add_inventory_item creates an inventory item', function () {
    $user = User::factory()->create();

    $result = decodeResponse(callTool(app(AddInventoryItemTool::class), $user, [
        'name' => 'Olive Oil',
        'quantity' => 2,
    ]));

    expect($result['name'])->toBe('Olive Oil');
    expect($result['quantity'])->toBe(2);
    expect($result['user_id'])->toBe($user->id);

    $this->assertDatabaseHas('inventory_items', [
        'name' => 'Olive Oil',
        'user_id' => $user->id,
    ]);
});

test('add_inventory_item fails validation without required fields', function () {
    $user = User::factory()->create();

    expect(fn () => callTool(app(AddInventoryItemTool::class), $user, []))
        ->toThrow(Illuminate\Validation\ValidationException::class);
});

test('add_inventory_item accepts optional stock_location_id', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);

    $result = decodeResponse(callTool(app(AddInventoryItemTool::class), $user, [
        'name' => 'Batteries AA',
        'quantity' => 8,
        'stock_location_id' => $location->id,
    ]));

    expect($result['stock_location_id'])->toBe($location->id);
});

// ---------------------------------------------------------------------------
// UpdateInventoryItemTool
// ---------------------------------------------------------------------------

test('update_inventory_item updates an item belonging to the user', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Old Name', 'quantity' => 1]);

    $result = decodeResponse(callTool(app(UpdateInventoryItemTool::class), $user, [
        'id' => $item->id,
        'name' => 'New Name',
        'quantity' => 5,
    ]));

    expect($result['name'])->toBe('New Name');
    expect($result['quantity'])->toBe(5);
    $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'name' => 'New Name']);
});

test('update_inventory_item cannot update another user\'s item', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $owner->id]);

    expect(fn () => callTool(app(UpdateInventoryItemTool::class), $other, [
        'id' => $item->id,
        'name' => 'Hacked',
        'quantity' => 99,
    ]))->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ---------------------------------------------------------------------------
// AdjustItemQuantityTool
// ---------------------------------------------------------------------------

test('adjust_item_quantity increases quantity', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 3]);

    $result = decodeResponse(callTool(app(AdjustItemQuantityTool::class), $user, [
        'inventory_item_id' => $item->id,
        'adjustment' => 5,
    ]));

    expect($result['new_quantity'])->toBe(8);
    expect($result['adjustment'])->toBe(5);
    $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 8]);
});

test('adjust_item_quantity decreases quantity', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 10]);

    $result = decodeResponse(callTool(app(AdjustItemQuantityTool::class), $user, [
        'inventory_item_id' => $item->id,
        'adjustment' => -4,
    ]));

    expect($result['new_quantity'])->toBe(6);
    $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 6]);
});

test('adjust_item_quantity clamps quantity at zero', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 2]);

    $result = decodeResponse(callTool(app(AdjustItemQuantityTool::class), $user, [
        'inventory_item_id' => $item->id,
        'adjustment' => -100,
    ]));

    expect($result['new_quantity'])->toBe(0);
    $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 0]);
});

test('adjust_item_quantity cannot adjust another user\'s item', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $owner->id, 'quantity' => 5]);

    expect(fn () => callTool(app(AdjustItemQuantityTool::class), $other, [
        'inventory_item_id' => $item->id,
        'adjustment' => -5,
    ]))->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ---------------------------------------------------------------------------
// FindLowStockItemsTool
// ---------------------------------------------------------------------------

test('find_low_stock_items returns items below reorder_point', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 1, 'reorder_point' => 5]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 10, 'reorder_point' => 5]); // Above reorder point

    $result = decodeResponse(callTool(app(FindLowStockItemsTool::class), $user, []));

    expect($result['count'])->toBe(1);
});

test('find_low_stock_items uses custom threshold when provided', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 3]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 10]);

    $result = decodeResponse(callTool(app(FindLowStockItemsTool::class), $user, ['threshold' => 5]));

    expect($result['count'])->toBe(1);
    expect($result['items'][0]['quantity'])->toBe(3);
});

test('find_low_stock_items does not return other users items', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $other->id, 'quantity' => 0, 'reorder_point' => 5]);

    $result = decodeResponse(callTool(app(FindLowStockItemsTool::class), $user, []));

    expect($result['count'])->toBe(0);
});

// ---------------------------------------------------------------------------
// CreateShoppingListTool
// ---------------------------------------------------------------------------

test('create_shopping_list creates a list', function () {
    $user = User::factory()->create();

    $result = decodeResponse(callTool(app(CreateShoppingListTool::class), $user, [
        'name' => 'Weekly Groceries',
    ]));

    expect($result['name'])->toBe('Weekly Groceries');
    expect($result['user_id'])->toBe($user->id);
    $this->assertDatabaseHas('shopping_lists', ['name' => 'Weekly Groceries', 'user_id' => $user->id]);
});

test('create_shopping_list fails without a name', function () {
    $user = User::factory()->create();

    expect(fn () => callTool(app(CreateShoppingListTool::class), $user, []))
        ->toThrow(Illuminate\Validation\ValidationException::class);
});

// ---------------------------------------------------------------------------
// AddItemToShoppingListTool
// ---------------------------------------------------------------------------

test('add_item_to_shopping_list adds a standalone item by name', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    $result = decodeResponse(callTool(app(AddItemToShoppingListTool::class), $user, [
        'shopping_list_id' => $list->id,
        'name' => 'Milk',
        'quantity' => 2,
    ]));

    expect($result['name'])->toBe('Milk');
    expect($result['inventory_item_id'])->toBeNull();
    $this->assertDatabaseHas('shopping_list_items', ['shopping_list_id' => $list->id, 'name' => 'Milk']);
});

test('add_item_to_shopping_list links an inventory item when inventory_item_id is given', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Olive Oil']);

    $result = decodeResponse(callTool(app(AddItemToShoppingListTool::class), $user, [
        'shopping_list_id' => $list->id,
        'inventory_item_id' => $item->id,
        'quantity' => 1,
    ]));

    expect($result['inventory_item_id'])->toBe($item->id);
    expect($result['name'])->toBe('Olive Oil');
});

test('add_item_to_shopping_list aborts when neither name nor inventory_item_id given', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id]);

    expect(fn () => callTool(app(AddItemToShoppingListTool::class), $user, [
        'shopping_list_id' => $list->id,
        'quantity' => 1,
    ]))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('add_item_to_shopping_list cannot add to another user\'s list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    expect(fn () => callTool(app(AddItemToShoppingListTool::class), $other, [
        'shopping_list_id' => $list->id,
        'name' => 'Milk',
        'quantity' => 1,
    ]))->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ---------------------------------------------------------------------------
// MarkShoppingListCompleteTool
// ---------------------------------------------------------------------------

test('mark_shopping_list_complete marks a list as completed', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id, 'is_completed' => false]);

    $result = decodeResponse(callTool(app(MarkShoppingListCompleteTool::class), $user, [
        'shopping_list_id' => $list->id,
    ]));

    expect($result['is_completed'])->toBeTrue();
    $this->assertDatabaseHas('shopping_lists', ['id' => $list->id, 'is_completed' => true]);
});

test('mark_shopping_list_complete cannot mark another user\'s list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    expect(fn () => callTool(app(MarkShoppingListCompleteTool::class), $other, [
        'shopping_list_id' => $list->id,
    ]))->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ---------------------------------------------------------------------------
// GetDashboardTool
// ---------------------------------------------------------------------------

test('get_dashboard returns summary data for the user', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 0, 'reorder_point' => 5]);

    $result = decodeResponse(callTool(app(GetDashboardTool::class), $user, []));

    expect($result)->toHaveKey('total_items');
    expect($result)->toHaveKey('low_stock_items');
});

test('get_dashboard does not include other users data', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    InventoryItem::factory()->count(3)->create(['user_id' => $other->id]);

    $result = decodeResponse(callTool(app(GetDashboardTool::class), $user, []));

    expect($result['total_items'])->toBe(0);
});

// ---------------------------------------------------------------------------
// GetInventoryItemsTool
// ---------------------------------------------------------------------------

test('get_inventory_items returns paginated items for the user', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->count(3)->create(['user_id' => $user->id]);

    $result = decodeResponse(callTool(app(GetInventoryItemsTool::class), $user, []));

    expect($result['total'])->toBe(3);
    expect(count($result['items']))->toBe(3);
});

test('get_inventory_items filters by search term', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Olive Oil']);
    InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Butter']);

    $result = decodeResponse(callTool(app(GetInventoryItemsTool::class), $user, ['search' => 'Olive']));

    expect($result['total'])->toBe(1);
    expect($result['items'][0]['name'])->toBe('Olive Oil');
});

test('get_inventory_items filters by stock location', function () {
    $user = User::factory()->create();
    $location = StockLocation::factory()->create(['user_id' => $user->id]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'stock_location_id' => $location->id]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'stock_location_id' => null]);

    $result = decodeResponse(callTool(app(GetInventoryItemsTool::class), $user, ['stock_location_id' => $location->id]));

    expect($result['total'])->toBe(1);
});

test('get_inventory_items does not return other users items', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    InventoryItem::factory()->count(2)->create(['user_id' => $other->id]);

    $result = decodeResponse(callTool(app(GetInventoryItemsTool::class), $user, []));

    expect($result['total'])->toBe(0);
});

// ---------------------------------------------------------------------------
// GetInventoryItemTool
// ---------------------------------------------------------------------------

test('get_inventory_item returns a single item by id', function () {
    $user = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $user->id, 'name' => 'Garlic']);

    $result = decodeResponse(callTool(app(GetInventoryItemTool::class), $user, ['id' => $item->id]));

    expect($result['name'])->toBe('Garlic');
    expect($result['id'])->toBe($item->id);
});

test('get_inventory_item cannot retrieve another user\'s item', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $item = InventoryItem::factory()->create(['user_id' => $owner->id]);

    expect(fn () => callTool(app(GetInventoryItemTool::class), $other, ['id' => $item->id]))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ---------------------------------------------------------------------------
// GetStockLocationsTool
// ---------------------------------------------------------------------------

test('get_stock_locations returns locations for the user', function () {
    $user = User::factory()->create();
    StockLocation::factory()->count(2)->create(['user_id' => $user->id]);

    $result = decodeResponse(callTool(app(GetStockLocationsTool::class), $user, []));

    expect($result['total'])->toBe(2);
    expect(count($result['locations']))->toBe(2);
});

test('get_stock_locations does not return other users locations', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    StockLocation::factory()->count(3)->create(['user_id' => $other->id]);

    $result = decodeResponse(callTool(app(GetStockLocationsTool::class), $user, []));

    expect($result['total'])->toBe(0);
});

// ---------------------------------------------------------------------------
// GetShoppingListsTool
// ---------------------------------------------------------------------------

test('get_shopping_lists returns lists for the user', function () {
    $user = User::factory()->create();
    ShoppingList::factory()->count(2)->create(['user_id' => $user->id]);

    $result = decodeResponse(callTool(app(GetShoppingListsTool::class), $user, []));

    expect($result['total'])->toBe(2);
    expect(count($result['lists']))->toBe(2);
});

test('get_shopping_lists does not return other users lists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    ShoppingList::factory()->count(3)->create(['user_id' => $other->id]);

    $result = decodeResponse(callTool(app(GetShoppingListsTool::class), $user, []));

    expect($result['total'])->toBe(0);
});

// ---------------------------------------------------------------------------
// GetShoppingListTool
// ---------------------------------------------------------------------------

test('get_shopping_list returns a single list by id', function () {
    $user = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $user->id, 'name' => 'Weekly Shop']);

    $result = decodeResponse(callTool(app(GetShoppingListTool::class), $user, ['id' => $list->id]));

    expect($result['name'])->toBe('Weekly Shop');
    expect($result['id'])->toBe($list->id);
});

test('get_shopping_list cannot retrieve another user\'s list', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $list = ShoppingList::factory()->create(['user_id' => $owner->id]);

    expect(fn () => callTool(app(GetShoppingListTool::class), $other, ['id' => $list->id]))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
