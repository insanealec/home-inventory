<?php

use App\Actions\Token\CreateToken;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can create token for authenticated user', function () {
    $user = User::factory()->create();

    $action = app(CreateToken::class);
    $token = $action->handle($user, 'Test Token', ['*']);

    expect($token)->not()->toBeNull();
    expect($token->accessToken)->not()->toBeEmpty();
});

test('creates token with specified name', function () {
    $user = User::factory()->create();

    $action = app(CreateToken::class);
    $token = $action->handle($user, 'My API Token', ['*']);

    expect($user->tokens()->first()->name)->toBe('My API Token');
});

test('creates token with specified abilities', function () {
    $user = User::factory()->create();

    $action = app(CreateToken::class);
    $token = $action->handle($user, 'Limited Token', ['read', 'write']);

    $ability = $user->tokens()->first()->abilities;
    expect($ability)->toContain('read');
    expect($ability)->toContain('write');
});

test('creates token with wildcard ability by default', function () {
    $user = User::factory()->create();

    $action = app(CreateToken::class);
    $token = $action->handle($user, 'Default Token');

    $abilities = $user->tokens()->first()->abilities;
    expect($abilities)->toContain('*');
});

test('returns null when user is null', function () {
    $action = app(CreateToken::class);
    $token = $action->handle(null, 'Test Token');

    expect($token)->toBeNull();
});

test('token is associated with user', function () {
    $user = User::factory()->create();

    $action = app(CreateToken::class);
    $token = $action->handle($user, 'Test Token', ['*']);

    expect($user->tokens()->count())->toBe(1);
    expect($user->tokens()->first()->tokenable_id)->toBe($user->id);
});
