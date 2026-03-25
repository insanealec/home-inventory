# Laravel Fortify Development

Fortify is a headless authentication backend. It provides routes and controllers; views and responses are customised by the application.

## Key Locations
- **Routes**: Use `list-routes` with `action: "Fortify"` to see all registered endpoints
- **Actions**: `app/Actions/Fortify/` — customisable business logic (user creation, password validation)
- **Config**: `config/fortify.php` — features, guards, rate limiters, username field
- **Contracts**: `Laravel\Fortify\Contracts\` — override `LoginResponse`, `LogoutResponse`, etc.
- **Views**: Set in `FortifyServiceProvider::boot()` using `Fortify::loginView()`, `Fortify::registerView()`, etc.

## Available Features (enable in `config/fortify.php`)

```php
Features::registration()
Features::resetPasswords()
Features::emailVerification()   // requires User to implement MustVerifyEmail
Features::updateProfileInformation()
Features::updatePasswords()
Features::twoFactorAuthentication()
```

## Key Endpoints

| Feature                | Method | Endpoint                                    |
|------------------------|--------|---------------------------------------------|
| Login                  | POST   | `/login`                                    |
| Logout                 | POST   | `/logout`                                   |
| Register               | POST   | `/register`                                 |
| Password Reset Request | POST   | `/forgot-password`                          |
| Password Reset         | POST   | `/reset-password`                           |
| Email Verify Notice    | GET    | `/email/verify`                             |
| Resend Verification    | POST   | `/email/verification-notification`          |
| Enable 2FA             | POST   | `/user/two-factor-authentication`           |
| 2FA Challenge          | POST   | `/two-factor-challenge`                     |
| Get QR Code            | GET    | `/user/two-factor-qr-code`                  |

## Customisation Patterns

### Custom Login Logic
```php
Fortify::authenticateUsing(function (Request $request) {
    $user = User::where('email', $request->email)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        return $user;
    }
});
```

### Custom Responses
Override in `AppServiceProvider`:
```php
$this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
```

### Registration
Customise `app/Actions/Fortify/CreateNewUser.php` for user creation logic, validation, and extra fields.

## Common Pitfalls
- Forgetting to set view callbacks in `FortifyServiceProvider::boot()`
- Not adding `TwoFactorAuthenticatable` trait to User model before enabling 2FA
- Expecting Fortify to provide views — it's headless, you provide them
