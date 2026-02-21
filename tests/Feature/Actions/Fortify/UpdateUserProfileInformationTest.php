<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('can update user profile information', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    expect($user->fresh()->name)->toBe('New Name');
    expect($user->fresh()->email)->toBe('new@example.com');
});

test('requires name field', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(UpdateUserProfileInformation::class)->update($user, [
        'email' => 'new@example.com',
    ]);
});

test('requires email field', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => 'New Name',
    ]);
});

test('email must be valid', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => 'New Name',
        'email' => 'invalid-email',
    ]);
});

test('email must be unique except for own email', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $this->expectException(ValidationException::class);

    app(UpdateUserProfileInformation::class)->update($user1, [
        'name' => 'New Name',
        'email' => 'user2@example.com',
    ]);
});

test('can update to same email', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'user@example.com',
    ]);

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => 'New Name',
        'email' => 'user@example.com',
    ]);

    expect($user->fresh()->name)->toBe('New Name');
    expect($user->fresh()->email)->toBe('user@example.com');
});

test('name has max length constraint', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => str_repeat('a', 256),
        'email' => 'new@example.com',
    ]);
});
