<?php

use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

test('cannot update password with incorrect current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $this->expectException(ValidationException::class);

    app(UpdateUserPassword::class)->update($user, [
        'current_password' => 'WrongPassword123!',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);
});

test('requires current password field', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $this->expectException(ValidationException::class);

    app(UpdateUserPassword::class)->update($user, [
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);
});

test('new password must meet requirements', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $this->expectException(ValidationException::class);

    app(UpdateUserPassword::class)->update($user, [
        'current_password' => 'OldPassword123!',
        'password' => 'weak',
        'password_confirmation' => 'weak',
    ]);
});

test('new password must match confirmation', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $this->expectException(ValidationException::class);

    app(UpdateUserPassword::class)->update($user, [
        'current_password' => 'OldPassword123!',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'DifferentPassword123!',
    ]);
});
