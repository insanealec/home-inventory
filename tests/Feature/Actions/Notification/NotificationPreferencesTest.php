<?php

use App\Actions\Notification\GetNotificationPreferencesAction;
use App\Actions\Notification\UpdateNotificationPreferencesAction;
use App\Models\User;

// ---------------------------------------------------------------------------
// GetNotificationPreferencesAction
// ---------------------------------------------------------------------------

test('get preferences returns defaults when no preferences are stored', function () {
    $user = User::factory()->create(['notification_preferences' => null]);

    $prefs = app(GetNotificationPreferencesAction::class)->handle($user);

    expect($prefs)->toBe(['low_stock' => true, 'expiring_items' => true]);
});

test('get preferences merges stored values with defaults', function () {
    $user = User::factory()->create(['notification_preferences' => ['low_stock' => false]]);

    $prefs = app(GetNotificationPreferencesAction::class)->handle($user);

    expect($prefs['low_stock'])->toBeFalse();
    expect($prefs['expiring_items'])->toBeTrue(); // default applied
});

// ---------------------------------------------------------------------------
// UpdateNotificationPreferencesAction
// ---------------------------------------------------------------------------

test('update preferences persists valid preference keys', function () {
    $user = User::factory()->create();

    app(UpdateNotificationPreferencesAction::class)->handle($user, ['low_stock' => false]);

    expect($user->fresh()->notification_preferences['low_stock'])->toBeFalse();
});

test('update preferences ignores unknown keys', function () {
    $user = User::factory()->create();

    app(UpdateNotificationPreferencesAction::class)->handle($user, [
        'low_stock' => false,
        'unknown_type' => true,
    ]);

    $stored = $user->fresh()->notification_preferences;
    expect($stored)->not->toHaveKey('unknown_type');
    expect($stored)->toHaveKey('low_stock');
});

test('update preferences can re-enable a previously disabled preference', function () {
    $user = User::factory()->create(['notification_preferences' => ['low_stock' => false]]);

    app(UpdateNotificationPreferencesAction::class)->handle($user, ['low_stock' => true]);

    expect($user->fresh()->notification_preferences['low_stock'])->toBeTrue();
});

// ---------------------------------------------------------------------------
// GET /api/user/notification-preferences
// ---------------------------------------------------------------------------

test('GET notification preferences returns preferences with defaults', function () {
    $user = User::factory()->create(['notification_preferences' => ['low_stock' => false]]);

    $this->actingAs($user)
        ->getJson('/api/user/notification-preferences')
        ->assertOk()
        ->assertJson(['low_stock' => false, 'expiring_items' => true]);
});

test('GET notification preferences requires authentication', function () {
    $this->getJson('/api/user/notification-preferences')->assertUnauthorized();
});

// ---------------------------------------------------------------------------
// PUT /api/user/notification-preferences
// ---------------------------------------------------------------------------

test('PUT notification preferences updates and returns merged result', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson('/api/user/notification-preferences', [
            'preferences' => ['low_stock' => false],
        ])
        ->assertOk()
        ->assertJson(['low_stock' => false, 'expiring_items' => true]);
});

test('PUT notification preferences requires authentication', function () {
    $this->putJson('/api/user/notification-preferences', ['preferences' => []])
        ->assertUnauthorized();
});

test('PUT notification preferences rejects non-boolean values', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->putJson('/api/user/notification-preferences', [
            'preferences' => ['low_stock' => 'yes'],
        ])
        ->assertUnprocessable();
});
