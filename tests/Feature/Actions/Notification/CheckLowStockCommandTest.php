<?php

use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

test('command sends low stock notification to users with items below reorder point', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 1, 'reorder_point' => 5]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertSentTo($user, LowStockNotification::class, function ($notification) {
        return $notification->items->count() === 1;
    });
});

test('command does not notify users with all items above reorder point', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 10, 'reorder_point' => 5]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});

test('command does not notify users with no reorder point set', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 0, 'reorder_point' => null]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});

test('command skips users who have opted out of low stock notifications', function () {
    Notification::fake();

    $user = User::factory()->create(['notification_preferences' => ['low_stock' => false]]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 0, 'reorder_point' => 5]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});

test('command notifies users who have opted in explicitly', function () {
    Notification::fake();

    $user = User::factory()->create(['notification_preferences' => ['low_stock' => true]]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 0, 'reorder_point' => 5]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertSentTo($user, LowStockNotification::class);
});

test('command notifies users with no preferences set (default opted in)', function () {
    Notification::fake();

    $user = User::factory()->create(['notification_preferences' => null]);
    InventoryItem::factory()->create(['user_id' => $user->id, 'quantity' => 0, 'reorder_point' => 5]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertSentTo($user, LowStockNotification::class);
});

test('command does not notify other users for items they do not own', function () {
    Notification::fake();

    $user = User::factory()->create();
    $other = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $other->id, 'quantity' => 0, 'reorder_point' => 5]);

    $this->artisan('inventory:check-low-stock')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});
