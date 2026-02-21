<?php

use App\Actions\Token\CreateToken;
use App\Actions\Token\DestroyToken;
use App\Models\User;

test('can destroy token by id', function () {
    $user = User::factory()->create();

    // Create a token first
    $createTokenAction = app(CreateToken::class);
    $tokenResponse = $createTokenAction->handle($user, 'Test Token to Delete');

    // Get the token ID from the user's tokens
    $tokenId = $user->tokens()->first()->id;

    // Destroy the token
    $destroyAction = app(DestroyToken::class);
    $result = $destroyAction->handle($user, $tokenId);

    expect($result)->toBeTrue();
    expect($user->tokens()->where('id', $tokenId)->exists())->toBeFalse();
});

test('returns false when token does not exist', function () {
    $user = User::factory()->create();

    $action = app(DestroyToken::class);
    $result = $action->handle($user, 'non-existent-id');

    expect($result)->toBeFalse();
});

test('returns empty collection when user is null', function () {
    $action = app(DestroyToken::class);
    $result = $action->handle(null, 'some-token-id');

    expect($result)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($result)->toHaveCount(0);
});

test('deletes only the specified token', function () {
    $user = User::factory()->create();

    // Create multiple tokens
    $createTokenAction = app(CreateToken::class);
    $createTokenAction->handle($user, 'Token 1');
    $createTokenAction->handle($user, 'Token 2');
    $createTokenAction->handle($user, 'Token 3');

    expect($user->tokens()->count())->toBe(3);

    // Get and delete a specific token
    $tokenId = $user->tokens()->where('name', 'Token 2')->first()->id;

    $destroyAction = app(DestroyToken::class);
    $destroyAction->handle($user, $tokenId);

    expect($user->tokens()->count())->toBe(2);
    expect($user->tokens()->where('name', 'Token 2')->exists())->toBeFalse();
});
