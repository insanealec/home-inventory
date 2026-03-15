<?php

use App\Actions\Dashboard\GetDashboardSummaryAction;
use App\Models\InventoryItem;
use App\Models\ShoppingList;
use App\Models\StockLocation;
use App\Models\User;

test('can get dashboard summary for user', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->count(5)->create(['user_id' => $user->id]);
    StockLocation::factory()->count(3)->create(['user_id' => $user->id]);
    ShoppingList::factory()->count(2)->create(['user_id' => $user->id, 'is_completed' => false]);
    ShoppingList::factory()->create(['user_id' => $user->id, 'is_completed' => true]);

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['total_items'])->toBe(5);
    expect($summary['total_locations'])->toBe(3);
    expect($summary['active_shopping_lists'])->toBe(2);
});

test('dashboard includes low stock items', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Low Stock Item',
        'quantity' => 2,
        'reorder_point' => 5,
    ]);
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Normal Stock',
        'quantity' => 10,
        'reorder_point' => 5,
    ]);

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['low_stock_items'])->toHaveCount(1);
    expect($summary['low_stock_items'][0]->name)->toBe('Low Stock Item');
});

test('dashboard includes expiring items within 30 days', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Expiring Soon',
        'expiration_date' => now()->addDays(10),
    ]);
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Expires Later',
        'expiration_date' => now()->addDays(60),
    ]);
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Already Expired',
        'expiration_date' => now()->subDays(5),
    ]);

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['expiring_items'])->toHaveCount(2);
    expect($summary['expiring_items']->pluck('name'))->toContain('Expiring Soon', 'Already Expired');
});

test('dashboard includes recently updated items', function () {
    $user = User::factory()->create();
    $oldItem = InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old Item',
        'updated_at' => now()->subDays(10),
    ]);
    $newItem = InventoryItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'New Item',
        'updated_at' => now(),
    ]);

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['recent_items'])->toHaveCount(2);
    expect($summary['recent_items'][0]->name)->toBe('New Item');
    expect($summary['recent_items'][1]->name)->toBe('Old Item');
});

test('dashboard returns empty arrays when no data exists', function () {
    $user = User::factory()->create();

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['total_items'])->toBe(0);
    expect($summary['total_locations'])->toBe(0);
    expect($summary['active_shopping_lists'])->toBe(0);
    expect($summary['low_stock_items'])->toHaveCount(0);
    expect($summary['expiring_items'])->toHaveCount(0);
    expect($summary['recent_items'])->toHaveCount(0);
});

test('dashboard limits low stock items to 5', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->count(10)->create([
        'user_id' => $user->id,
        'quantity' => 1,
        'reorder_point' => 5,
    ]);

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['low_stock_items'])->toHaveCount(5);
});

test('dashboard limits expiring items to 5', function () {
    $user = User::factory()->create();
    for ($i = 1; $i <= 10; $i++) {
        InventoryItem::factory()->create([
            'user_id' => $user->id,
            'expiration_date' => now()->addDays($i),
        ]);
    }

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['expiring_items'])->toHaveCount(5);
});

test('dashboard limits recent items to 5', function () {
    $user = User::factory()->create();
    InventoryItem::factory()->count(10)->create(['user_id' => $user->id]);

    $action = app(GetDashboardSummaryAction::class);
    $summary = $action->handle($user);

    expect($summary['recent_items'])->toHaveCount(5);
});
