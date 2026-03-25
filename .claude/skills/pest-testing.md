# Pest 4 Testing

## When to Use
Creating new tests, modifying existing tests, debugging test failures, writing browser or architecture tests.

## Test Structure

All tests use **Pest 4 function style** — never PHPUnit class-based style:

```php
test('can create shopping list', function () {
    $user = User::factory()->create();

    $list = app(CreateShoppingListAction::class)->handle($user, ['name' => 'Weekly Shop']);

    expect($list)->toBeInstanceOf(ShoppingList::class);
    expect($list->user_id)->toBe($user->id);
});
```

## Running Tests

```bash
php artisan test --compact                              # all tests
php artisan test --compact --filter=TestName           # single test
php artisan test --compact tests/Feature/Actions/      # directory
php artisan make:test --pest Actions/MyActionTest      # create new
```

## Assertions

Use semantic assertion methods — not raw `assertStatus()`:

| Use                  | Instead of          |
|----------------------|---------------------|
| `assertSuccessful()` | `assertStatus(200)` |
| `assertNotFound()`   | `assertStatus(404)` |
| `assertForbidden()`  | `assertStatus(403)` |
| `assertCreated()`    | `assertStatus(201)` |

## Test Data

- Always use **factories** — never hardcode record IDs (e.g., `category_id: 1`)
- If a model has no factory, create it directly via `Model::create([...])`
- Use `RefreshDatabase` for clean state

```php
$user = User::factory()->create();
$list = ShoppingList::factory()->create(['user_id' => $user->id]);
```

## Datasets

Use datasets for repetitive validation tests:

```php
it('rejects invalid quantities', function (int $qty) {
    expect(fn() => $action->handle($user, $list->id, $qty))
        ->toThrow(InvalidArgumentException::class);
})->with([-1, -100]);
```

## Architecture Tests

```php
arch('actions follow conventions')
    ->expect('App\Actions')
    ->toHaveSuffix('Action');
```

## Common Pitfalls
- Using PHPUnit class-based format instead of Pest functions
- Hardcoding IDs without creating the record first
- Using `assertStatus(200)` instead of `assertSuccessful()`
- Forgetting `RefreshDatabase` when tests affect the database
- Not importing models/actions used in the test file
