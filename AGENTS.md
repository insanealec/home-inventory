# Agent Guidelines

## Project Purpose
This is a home-inventory service intended to help people in homes keep track of their items.
Ideal situation is using the API/website to add items to locations in their home so they can track where things are and what they have.

## Core Environment
- **PHP**: 8.5.1
- **Laravel**: 12 (LARAVEL)
- **Fortify**: 1 (FORTIFY)
- **Sanctum**: 4 (SANCTUM)
- **Pest**: 4 (PEST)
- **Vue**: 3 (VUE)
- **TailwindCSS**: 4 (TAILWINDCSS)

## Essential Skills
- `pest-testing` — Tests applications using Pest 4 framework
- `tailwindcss-development` — Styles using Tailwind CSS v4 utilities
- `developing-with-fortify` — Laravel Fortify authentication backend

## Key Rules

### Do Things the Laravel Way
- Use `php artisan make:` commands for creating files
- Always use Eloquent relationships with return type hints
- Use Form Request classes for validation instead of inline validation
- Prefer named routes and `route()` function for URL generation
- Use queued jobs for time-consuming operations

### Database Standards
- Avoid `DB::` - prefer `Model::query()`
- Generate code using Laravel's ORM capabilities instead of bypassing them
- Prevent N+1 query problems through eager loading
- When modifying columns, maintain all previous attributes
- Laravel 12 allows limiting eagerly loaded records natively

### Code Style Guidelines
- Use PHP 8 constructor property promotion
- Always use explicit return type declarations
- Use appropriate PHP type hints for method parameters
- Follow PSR-12 coding standards
- Use curly braces for ALL control structures
- Use descriptive variable and method names

### Testing Approach
- Use Pest 4 framework - `php artisan make:test --pest {name}`
- Run tests with: `php artisan test --compact`
- Write both unit and feature tests
- Use factory methods for test data
- Follow Arrange-Act-Assert pattern

### Build & Development
- `composer install` - Install project dependencies
- `npm install` - Install frontend dependencies
- `npm run build` - Build frontend assets
- `npm run dev` - Run development server
- `php artisan migrate` - Run database migrations
- `php artisan serve` - Run Laravel development server
- `composer run dev` - Run both frontend and backend servers

## Critical Tools
- `list-artisan-commands` - Check available Artisan parameters
- `search-docs` - Version-specific Laravel documentation
- `tinker` - Execute PHP to debug code/query models directly
- `database-query` - Read from database only
- `browser-logs` - Read browser errors/exceptions