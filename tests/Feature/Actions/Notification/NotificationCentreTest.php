<?php

use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Collection;

// ---------------------------------------------------------------------------
// GET /api/notifications
// ---------------------------------------------------------------------------

test('GET notifications returns the authenticated users notifications', function () {
    $user = User::factory()->create();
    $user->notify(new LowStockNotification(new Collection()));

    $this->actingAs($user)
        ->getJson('/api/notifications')
        ->assertOk()
        ->assertJsonCount(1);
});

test('GET notifications does not return other users notifications', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $other->notify(new LowStockNotification(new Collection()));

    $this->actingAs($user)
        ->getJson('/api/notifications')
        ->assertOk()
        ->assertJsonCount(0);
});

test('GET notifications requires authentication', function () {
    $this->getJson('/api/notifications')->assertUnauthorized();
});

// ---------------------------------------------------------------------------
// PUT /api/notifications/{id}  (mark as read)
// ---------------------------------------------------------------------------

test('PUT notification marks it as read', function () {
    $user = User::factory()->create();
    $user->notify(new LowStockNotification(new Collection()));

    $notification = $user->notifications()->first();
    expect($notification->read_at)->toBeNull();

    $this->actingAs($user)
        ->putJson("/api/notifications/{$notification->id}")
        ->assertOk();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('PUT notification returns 404 for another users notification', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $other->notify(new LowStockNotification(new Collection()));

    $notification = $other->notifications()->first();

    $this->actingAs($user)
        ->putJson("/api/notifications/{$notification->id}")
        ->assertNotFound();
});

// ---------------------------------------------------------------------------
// DELETE /api/notifications/{id}  (dismiss)
// ---------------------------------------------------------------------------

test('DELETE notification removes it', function () {
    $user = User::factory()->create();
    $user->notify(new LowStockNotification(new Collection()));

    $notification = $user->notifications()->first();

    $this->actingAs($user)
        ->deleteJson("/api/notifications/{$notification->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});

test('DELETE notification returns 404 for another users notification', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $other->notify(new LowStockNotification(new Collection()));

    $notification = $other->notifications()->first();

    $this->actingAs($user)
        ->deleteJson("/api/notifications/{$notification->id}")
        ->assertNotFound();
});
