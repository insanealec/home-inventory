# Home Inventory — Claude Agent Guidelines

## Project Purpose
A home-inventory service helping people track items across locations in their home. The API and website let users add items to locations, track where things are, and what they have.

## Core Stack
- **PHP** 8.5.1 / **Laravel** 12
- **Authentication**: Fortify 1 + Sanctum 4
- **Testing**: Pest 4
- **Frontend**: Angular (standalone + Signals) + TailwindCSS 4 *(migrating from Vue 3)*

## Key Skills
Import these files when working on the relevant domain:

- @.claude/skills/pest-testing.md — Writing and running Pest 4 tests
- @.claude/skills/developing-with-fortify.md — Laravel Fortify auth backend
- @.claude/skills/tailwindcss-development.md — Tailwind CSS v4 utilities
- @.claude/skills/angular-migration/SKILL.md — Angular frontend conventions, component patterns, services, routing

## Laravel Conventions

### Do Things the Laravel Way
- Use `php artisan make:` commands for creating files
- Always use Eloquent relationships with explicit return type hints (`HasMany`, `BelongsTo`, etc.)
- Prefer named routes and the `route()` helper for URL generation
- Use queued jobs for time-consuming operations

### Validation
- Use the `AsAction` trait from `lorisleiva/laravel-actions` — define a `rules()` method, not inline `Validator::make()`
- Validation lives in the action, not in a separate Form Request, to keep logic co-located

### Database
- Avoid raw `DB::` calls — prefer `Model::query()` and Eloquent
- Always eager-load relationships to prevent N+1 queries
- When modifying columns in migrations, preserve all previous attributes
- Use `whereHas()` to scope queries through relationships rather than adding shortcut `hasMany` relationships that skip natural data hierarchies

### Code Style
- PHP 8 constructor property promotion where appropriate
- Explicit return type declarations on all methods
- PSR-12 coding standards
- Curly braces on ALL control structures

## Action Pattern

All business logic lives in `app/Actions/`. Follow the `AsAction` trait pattern from `lorisleiva/laravel-actions`:

```php
class CreateShoppingListAction
{
    use AsAction;

    public function handle(User $user, array $data): ShoppingList
    {
        return $user->shoppingLists()->create($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function asController(Request $request): ShoppingList
    {
        return $this->handle($request->user(), $request->all());
    }
}
```

**Ownership scoping for nested resources:** Items that belong to a user through an intermediate model (e.g., `ShoppingListItem` → `ShoppingList` → `User`) must be scoped through a `whereHas()` chain, not via a direct shortcut relationship on `User`:

```php
ShoppingListItem::whereHas('shoppingList', function ($query) use ($user) {
    $query->where('user_id', $user->id);
})->findOrFail($id);
```

## Testing

Read @.claude/skills/pest-testing.md before writing any test.

- All tests use **Pest 4** function style (`test()`, `it()`, `expect()`) — not PHPUnit class-based style
- Run: `php artisan test --compact`
- Filter: `php artisan test --compact --filter=TestName`
- Make: `php artisan make:test --pest {Name}`
- Use factory methods for test data; never hardcode IDs
- Follow Arrange → Act → Assert structure

## Models & Relationships

```
User
  ├── hasMany → ShoppingList
  │     └── hasMany → ShoppingListItem
  │           ├── belongsTo → ShoppingCategory (optional)
  │           └── belongsTo → InventoryItem (optional, nullable)
  ├── hasMany → InventoryItem
  ├── hasMany → ShoppingCategory
  └── hasMany → StockLocation
        └── hasMany → InventoryItem
```

## MCP Tools Available (via Laravel Boost)
- `list-artisan-commands` — check available Artisan commands and their parameters
- `search-docs` — version-specific Laravel/Pest/Tailwind documentation lookup
- `tinker` — execute PHP to debug code or query models directly
- `database-query` — read-only database queries for inspection
- `list-routes` — list registered routes, filter by action or method

## Dev Commands
```bash
composer install        # Install PHP dependencies
npm install             # Install frontend dependencies
npm run build           # Build frontend assets
npm run dev             # Run Vite dev server
php artisan migrate     # Run database migrations
php artisan serve       # Run Laravel dev server
composer run dev        # Run both frontend and backend servers
php artisan test --compact  # Run all tests
```
