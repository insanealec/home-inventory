<?php

use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\ItemExpiringNotification;
use Illuminate\Support\Facades\Notification;

test('command sends expiry notification for items expiring within the default window', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'expiration_date' => now()->addDays(10),
    ]);

    $this->artisan('inventory:check-expiring-items')->assertSuccessful();

    Notification::assertSentTo($user, ItemExpiringNotification::class, function ($notification) {
        return $notification->items->count() === 1 && $notification->windowDays === 30;
    });
});

test('command does not notify for items expiring beyond the window', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'expiration_date' => now()->addDays(60),
    ]);

    $this->artisan('inventory:check-expiring-items')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});

test('command does not notify for items with no expiration date', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create(['user_id' => $user->id, 'expiration_date' => null]);

    $this->artisan('inventory:check-expiring-items')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});

test('command respects custom --days option', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'expiration_date' => now()->addDays(45),
    ]);

    $this->artisan('inventory:check-expiring-items', ['--days' => 60])->assertSuccessful();

    Notification::assertSentTo($user, ItemExpiringNotification::class, function ($notification) {
        return $notification->windowDays === 60;
    });
});

test('command skips users who have opted out of expiry notifications', function () {
    Notification::fake();

    $user = User::factory()->create(['notification_preferences' => ['expiring_items' => false]]);
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'expiration_date' => now()->addDays(5),
    ]);

    $this->artisan('inventory:check-expiring-items')->assertSuccessful();

    Notification::assertNothingSentTo($user);
});

test('command notifies users with no preferences set (default opted in)', function () {
    Notification::fake();

    $user = User::factory()->create(['notification_preferences' => null]);
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'expiration_date' => now()->addDays(5),
    ]);

    $this->artisan('inventory:check-expiring-items')->assertSuccessful();

    Notification::assertSentTo($user, ItemExpiringNotification::class);
});

test('command sends notification including already-expired items', function () {
    Notification::fake();

    $user = User::factory()->create();
    InventoryItem::factory()->create([
        'user_id' => $user->id,
        'expiration_date' => now()->subDays(2),
    ]);

    $this->artisan('inventory:check-expiring-items')->assertSuccessful();

    Notification::assertSentTo($user, ItemExpiringNotification::class);
});
