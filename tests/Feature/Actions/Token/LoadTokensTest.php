<?php

use App\Actions\Token\CreateToken;
use App\Actions\Token\LoadTokens;
use App\Models\User;

test('can load all tokens for user', function () {
    $user = User::factory()->create();

    // Create tokens
    $createTokenAction = app(CreateToken::class);
    $createTokenAction->handle($user, 'Token 1');
    $createTokenAction->handle($user, 'Token 2');
    $createTokenAction->handle($user, 'Token 3');

    $action = app(LoadTokens::class);
    $tokens = $action->handle($user);

    expect($tokens)->toHaveCount(3);
});

test('returns empty collection when user has no tokens', function () {
    $user = User::factory()->create();

    $action = app(LoadTokens::class);
    $tokens = $action->handle($user);

    expect($tokens)->toHaveCount(0);
});

test('returns empty collection when user is null', function () {
    $action = app(LoadTokens::class);
    $tokens = $action->handle(null);

    expect($tokens)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($tokens)->toHaveCount(0);
});

test('tokens contain token data with formatted dates', function () {
    $user = User::factory()->create();

    // Create a token
    $createTokenAction = app(CreateToken::class);
    $createTokenAction->handle($user, 'Test Token');

    // Load tokens directly
    $action = app(LoadTokens::class);
    $tokens = $action->handle($user);

    expect($tokens)->toHaveCount(1);
    $token = $tokens->first();
    expect($token->name)->toBe('Test Token');
});

test('can load tokens for user with multiple tokens', function () {
    $user = User::factory()->create();

    // Create multiple tokens
    $createTokenAction = app(CreateToken::class);
    $createTokenAction->handle($user, 'API Token');
    $createTokenAction->handle($user, 'Mobile Token');

    $action = app(LoadTokens::class);
    $tokens = $action->handle($user);

    expect($tokens)->toHaveCount(2);
    $names = $tokens->pluck('name')->toArray();
    expect($names)->toContain('API Token');
    expect($names)->toContain('Mobile Token');
});
