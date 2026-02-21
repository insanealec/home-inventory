<?php

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

test('can reset user password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    app(ResetUserPassword::class)->reset($user, [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();
});

test('password must meet requirements', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(ResetUserPassword::class)->reset($user, [
        'password' => 'weak',
        'password_confirmation' => 'weak',
    ]);
});

test('password must match confirmation', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(ResetUserPassword::class)->reset($user, [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);
});

test('requires password field', function () {
    $user = User::factory()->create();

    $this->expectException(ValidationException::class);

    app(ResetUserPassword::class)->reset($user, []);
});
