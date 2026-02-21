<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('can create new user with valid data', function () {
    $user = app(CreateNewUser::class)->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
    expect(hash_equals($user->password, bcrypt('Password123!')))->toBeFalse();
});

test('requires name field', function () {
    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
});

test('requires email field', function () {
    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'name' => 'John Doe',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
});

test('email must be valid', function () {
    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
});

test('email must be unique', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'name' => 'Jane Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
});

test('password must meet requirements', function () {
    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'weak',
        'password_confirmation' => 'weak',
    ]);
});

test('password must match confirmation', function () {
    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password456!',
    ]);
});

test('name has max length constraint', function () {
    $this->expectException(ValidationException::class);

    app(CreateNewUser::class)->create([
        'name' => str_repeat('a', 256),
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);
});
