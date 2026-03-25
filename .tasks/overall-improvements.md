# Home Inventory — Overall Improvements & Missing Features

> Last updated: 2026-03-14
> Based on full codebase audit + research into comparable home inventory systems.
>
> **For agents:** Work through each section in order. Check off individual tasks as you complete them. Do not mark a section complete until all sub-tasks within it are checked. Update the Priority Summary checkboxes as whole sections are finished.

---

## 1. Immediate Gaps (Existing Code Not Yet Wired Up)

These are features where the backend work is largely done but not exposed or connected.

### 1.1 Shopping List API Routes

All 14 shopping list actions are implemented and tested but no API routes exist for them. Register the following in `routes/api.php` under the Sanctum `auth` middleware group:

- [x] `GET    /api/shopping-lists` → `GetShoppingListsByUserAction`
- [x] `POST   /api/shopping-lists` → `CreateShoppingListAction`
- [x] `GET    /api/shopping-lists/{id}` → `GetShoppingListAction`
- [x] `PUT    /api/shopping-lists/{id}` → `UpdateShoppingListAction`
- [x] `DELETE /api/shopping-lists/{id}` → `DeleteShoppingListAction`
- [x] `GET    /api/shopping-lists/{id}/items` → `GetShoppingListItemsAction`
- [x] `POST   /api/shopping-lists/{id}/items` → `CreateShoppingListItemAction`
- [x] `POST   /api/shopping-lists/{id}/items/bulk` → `AddMultipleItemsToShoppingListAction`
- [x] `PUT    /api/shopping-lists/{id}/items/bulk` → `UpdateShoppingListItemsBulkAction`
- [x] `GET    /api/shopping-lists/{id}/items/{itemId}` → `GetShoppingListItemAction`
- [x] `PUT    /api/shopping-lists/{id}/items/{itemId}` → `UpdateShoppingListItemAction`
- [x] `DELETE /api/shopping-lists/{id}/items/{itemId}` → `DeleteShoppingListItemAction`
- [x] `POST   /api/shopping-lists/{id}/items/from-inventory` → `AddInventoryItemToShoppingListAction`
- [x] `POST   /api/shopping-lists/{id}/items/standalone` → `AddStandaloneItemToShoppingListAction`
- [x] Write HTTP-layer feature tests for all registered routes

### 1.2 Shopping Category API + Actions

The `ShoppingCategory` model and migration exist but there are no actions or routes for CRUD. Categories carry `name`, `store_section`, `color`, and `sort_order` — useful for grouping a shopping list by store aisle.

- [x] Create `GetShoppingCategoriesAction`
- [x] Create `CreateShoppingCategoryAction`
- [x] Create `UpdateShoppingCategoryAction`
- [x] Create `DeleteShoppingCategoryAction`
- [x] Register API routes for all four actions
- [x] Write Pest tests for all four actions

### 1.3 Shopping List Frontend

No Vue pages or Pinia store exist for shopping lists despite full backend support.

- [x] Create `stores/shopping-list.ts` Pinia store with full CRUD
- [x] Create `stores/shopping-category.ts` Pinia store
- [x] Create `shopping-lists/Index.vue` — paginated list of shopping lists
- [x] Create `shopping-lists/Create.vue` — new list form
- [x] Create `shopping-lists/Show.vue` — list detail with item check-off
- [x] Create `shopping-lists/Update.vue` — edit list metadata
- [x] Create `ShoppingListItemList.vue` component with check-off UX, drag-to-reorder, and inline item editing
- [x] Add Vue router entries for all shopping list pages
- [x] Add shopping lists navigation link in `NavMain.vue`

### 1.4 Dashboard / Home Screen

The current dashboard only shows API token management and should be an actual inventory overview.

- [x] Add a `DashboardSummaryAction` (or extend the existing dashboard) that returns counts and alerts
- [x] Display count of total inventory items, locations, and active shopping lists
- [x] Display items at or below `reorder_point` / `min_stock_level`
- [x] Display items with `expiration_date` within the next 30 days
- [x] Display recently modified items
- [x] Add quick-action buttons (add item, add location, new shopping list)

---

## 2. Inventory Item Enhancements

### 2.1 Item Photos

Allow users to attach one or more photos to an inventory item — particularly useful for electronics, spare parts, and tools.

- [ ] Install and configure `spatie/laravel-medialibrary`
- [ ] Add `InteractsWithMedia` to `InventoryItem` model with a `photos` media collection
- [ ] Create `AddInventoryItemPhotoAction` — handles upload and association
- [ ] Create `DeleteInventoryItemPhotoAction` — on intentional deletion, also null out `photo_embedding` if set (see 13.4)
- [ ] Register `POST /api/inventory-items/{id}/photos` and `DELETE /api/inventory-items/{id}/photos/{photoId}`
- [ ] Update `LoadItem` / API resource to include photo URLs — use a helper that falls back to a placeholder asset when `getFirstMediaUrl('photos')` returns empty (file missing from disk but DB record still present), e.g. `$item->getFirstMediaUrl('photos') ?: asset('images/no-photo.svg')`
- [ ] Add image upload input to `Create.vue` and `Update.vue`
- [ ] Add thumbnail gallery to `Show.vue` — render the placeholder image when a photo URL is the fallback asset
- [ ] Write tests for photo upload and deletion actions

### 2.2 Barcode & QR Code Support

Allow scanning or entering a barcode to quickly look up or create an item.

- [ ] Add nullable `barcode` string column to `inventory_items` via migration (indexed)
- [ ] Add `barcode` to `InventoryItem` fillable and validation rules
- [ ] Create `LookupItemByBarcodeAction` — returns existing item or queries Open Food Facts / UPC Item DB
- [ ] Register `GET /api/inventory-items/barcode/{code}` route
- [ ] Integrate `html5-qrcode` or `@zxing/browser` in the Vue frontend for camera-based scanning
- [ ] Wire barcode scanner into the Create Item form to auto-fill name, description, and unit
- [ ] Write tests for barcode lookup (found / not found / external API fallback)

### 2.3 Item Tags / Custom Attributes

Free-form tags and key-value metadata for brand, model number, serial number, warranty expiry, colour, size, etc.

- [ ] Add `tags` JSON column to `inventory_items` (or create `item_tags` pivot table if searchability needed)
- [ ] Add `custom_attributes` JSON column to `inventory_items`
- [ ] Update `CreateItem` and `UpdateItem` validation rules to accept tags and custom_attributes
- [ ] Update `LoadItems` to support filtering/searching by tag
- [ ] Add tag input UI to item create/edit forms
- [ ] Write tests for tag filtering

### 2.4 Item Attachments / Documents

Store receipts, manuals, or warranty PDFs against an item.

- [ ] Add a `documents` media collection to `InventoryItem` (reuses `spatie/laravel-medialibrary` from 2.1)
- [ ] Create `AddInventoryItemDocumentAction` and `DeleteInventoryItemDocumentAction`
- [ ] Register corresponding API endpoints
- [ ] Add document upload UI to item show/edit pages
- [ ] Write tests for document attachment and deletion

### 2.5 Purchase History

Track when an item was purchased, at what price, and from where — useful for warranty tracking and spend reports.

- [ ] Create `ItemPurchase` model with fields: `id`, `inventory_item_id`, `purchased_at`, `unit_price`, `quantity`, `store_name`, `notes`
- [ ] Write and run migration for `item_purchases` table
- [ ] Add `hasMany → ItemPurchase` relationship to `InventoryItem`
- [ ] Create `RecordItemPurchaseAction`, `UpdateItemPurchaseAction`, `DeleteItemPurchaseAction`
- [ ] Register API endpoints for purchase history
- [ ] Add purchase history list and form to `Show.vue`
- [ ] Write tests for all purchase history actions

---

## 3. Stock Location Enhancements

### 3.1 Location Hierarchy / Nesting

Currently locations are flat (e.g. "Kitchen"). Support nested storage: Kitchen → Pantry → Top Shelf.

- [ ] Add nullable `parent_id` foreign key to `stock_locations` via migration
- [ ] Add `parent()` (`BelongsTo`) and `children()` (`HasMany`) relationships to `StockLocation`
- [ ] Update `LoadStockLocations` to optionally return a tree structure
- [ ] Update `DeleteStockLocation` to handle or block deletion of locations with children
- [ ] Update location picker in the frontend to a nested tree/select component
- [ ] Write tests for hierarchy creation and tree retrieval

### 3.2 Location Capacity / Notes

Add optional capacity and notes fields to locations.

- [ ] Add nullable `capacity` (integer) and `notes` (text) columns to `stock_locations` via migration
- [ ] Update `CreateStockLocation` and `UpdateStockLocation` validation rules
- [ ] Display capacity and notes in location show/edit views
- [ ] Optionally show a capacity usage indicator (items count vs. capacity) in the UI

### 3.3 Items-by-Location View

A dedicated endpoint and page showing all items in a given location — useful for physical audits.

- [ ] Add `GET /api/stock-locations/{id}/inventory-items` route returning paginated items with full detail
- [ ] Create corresponding action or extend `LoadItems` to support this endpoint
- [ ] Add an "Items in this location" tab or section to `StockLocations/Show.vue`
- [ ] Write tests for the new endpoint

---

## 4. Stock & Expiration Alerts

The `InventoryItem` model already has `reorder_point`, `min_stock_level`, `max_stock_level`, and `expiration_date` — but nothing currently acts on them.

### 4.1 Low Stock Notifications

- [x] Create `CheckLowStockAction` (`AsAction` + `AsCommand`) registered as `inventory:check-low-stock`
- [x] Register command in `routes/console.php` with daily schedule at 08:00
- [x] Query items where `quantity <= reorder_point`, grouped by user
- [x] Create `LowStockNotification` (Laravel Notification class) with mail + database channels
- [x] Write tests for the command and notification

### 4.2 Expiration Notifications

- [x] Create `CheckExpiringItemsAction` (`AsAction` + `AsCommand`) registered as `inventory:check-expiring-items`
- [x] Register with daily schedule in `routes/console.php` at 08:00
- [x] Query items where `expiration_date` is within a configurable window (default: 30 days, override via `--days`)
- [x] Create `ItemExpiringNotification` with mail + database channels
- [x] Write tests for the command and notification

### 4.3 In-App Notification Centre

> **Depends on:** 4.1 and 4.2 — these sections write to the `database` notification channel, which is what the notification centre reads. The `notifications` table stores all database-channel notifications; this section provides the API and UI to surface them in the app.

- [x] Ensure `notifications` table exists (migration created)
- [x] Create a `GET /api/notifications` endpoint returning unread + recent notifications
- [x] Create `PUT /api/notifications/{id}` to mark as read
- [x] Create `DELETE /api/notifications/{id}` to dismiss
- [x] Add notification bell icon to `NavMain.vue` with unread count badge and dropdown
- [x] Build notification drawer/dropdown listing alerts with mark-read, dismiss, and link to settings
- [x] Write tests for notification read/dismiss endpoints

### 4.4 User Notification Preferences

Users can opt in or out of each notification type individually. The known notification types for now are `low_stock` and `expiring_items`; new types added in future sections should be added to this list.

Preference shape (stored as JSON on the `users` table):
```json
{
  "low_stock": true,
  "expiring_items": true
}
```
Unset keys default to `true` (opted in) so existing users receive notifications without needing a migration of their preferences.

- [x] Add `notification_preferences` JSON column (nullable) to `users` table via migration
- [x] Create `UpdateNotificationPreferencesAction` — filters to known keys only, values must be booleans
- [x] Create `GetNotificationPreferencesAction` — returns prefs with defaults applied for missing keys
- [x] Register `GET /api/user/notification-preferences` route
- [x] Register `PUT /api/user/notification-preferences` route
- [x] Build `NotificationPreferences.vue` settings page with labelled toggles and descriptions
- [x] Register `/settings/notifications` route in Vue Router
- [ ] Add a subtle discoverability nudge on the inventory item create/edit forms (e.g. "Low stock alerts are on — manage in notification preferences") linking to the preferences page
- [x] `CheckLowStockAction` skips users where `low_stock` preference is `false`
- [x] `CheckExpiringItemsAction` skips users where `expiring_items` preference is `false`
- [x] Write tests for preferences update, GET with defaults, and enforcement in each command

---

## 5. Shopping List Enhancements

### 5.1 Auto-Replenish Inventory on List Completion

When a shopping list is marked complete, optionally increment quantities on linked `InventoryItem` records.

> **Per-item opt-in:** Auto-replenish should be controlled by a boolean flag on `InventoryItem` (e.g. `auto_replenish`, default `false`). The listener only updates quantity for items where this flag is set — useful for consumables like food or cleaning supplies but not for durable goods like tools. Add the flag to the item create/edit forms and to the `AddInventoryItemTool` and `UpdateInventoryItemTool` MCP tools.

- [ ] Add `auto_replenish` boolean column (default `false`) to `inventory_items` via migration
- [ ] Update `CreateItem` and `UpdateItem` validation rules to accept `auto_replenish`
- [ ] Update `AddInventoryItemTool` and `UpdateInventoryItemTool` schemas to include `auto_replenish`
- [ ] Create `ShoppingListCompleted` event
- [ ] Create `UpdateInventoryFromShoppingListListener` queued listener — only increments quantity for linked items where `auto_replenish = true`
- [ ] Register event → listener in `EventServiceProvider`
- [ ] Dispatch event from `UpdateShoppingListAction` when `is_completed` transitions to `true`
- [ ] Add `auto_replenish` toggle to the inventory item create/edit forms
- [ ] Add confirmation prompt in the frontend before marking a list complete
- [ ] Write tests for event dispatch and listener behaviour (replenish-enabled and replenish-disabled items)

### 5.2 List Templates / Recurring Lists

Allow saving a list as a reusable template.

- [ ] Add `is_template` boolean and `template_name` nullable string to `shopping_lists` via migration
- [ ] Create `CreateShoppingListFromTemplateAction` — duplicates list and items, resets completion state
- [ ] Register `POST /api/shopping-lists/{id}/duplicate` route
- [ ] Add "Save as template" option to shopping list update UI
- [ ] Add a template library page listing all `is_template = true` lists
- [ ] Write tests for template creation and duplication action

### 5.3 Estimated vs. Actual Cost Tracking

- [ ] Add `actual_price` decimal column to `shopping_list_items` via migration
- [ ] Add `budget` decimal column to `shopping_lists` via migration
- [ ] Update `UpdateShoppingListItemAction` validation to accept `actual_price`
- [ ] Add `total_estimated` and `total_actual` computed values to the shopping list API response
- [ ] Display budget vs. actual spend summary on the shopping list show page
- [ ] Write tests for cost computation

### 5.4 Store Assignment

Allow assigning a shopping list to a specific store.

- [ ] Add nullable `store_name` string column to `shopping_lists` via migration
- [ ] Update `CreateShoppingListAction` and `UpdateShoppingListAction` to accept `store_name`
- [ ] Display store name on list index and show pages
- [ ] Write tests for store name validation

### 5.5 Sort Items by Store Section

Auto-sort shopping list items by their category's `store_section` so users can shop aisle-by-aisle.

- [ ] Create `SortShoppingListItemsAction` — updates `sort_order` on all items based on `category.sort_order` / `store_section`
- [ ] Register `POST /api/shopping-lists/{id}/items/sort-by-aisle` route
- [ ] Add "Sort by aisle" button to the shopping list show page
- [ ] Write tests for the sort action

---

## 6. Multi-User Households

### 6.1 Household / Team Model

- [ ] Create `Household` model: `id`, `name`, `created_by`, timestamps
- [ ] Create `household_user` pivot: `household_id`, `user_id`, `role` (owner|member|viewer), `joined_at`
- [ ] Write and run migrations for both tables
- [ ] Add `households()` and `currentHousehold()` relationships to `User`
- [ ] Add nullable `household_id` foreign key to `stock_locations`, `inventory_items`, `shopping_lists`, `shopping_categories`
- [ ] Update all ownership scoping in actions to check household membership when `household_id` is set
- [ ] Write tests for household-scoped resource access

### 6.2 Invitations

- [ ] Create `HouseholdInvitation` model: `id`, `household_id`, `email`, `token`, `role`, `accepted_at`, `expires_at`
- [ ] Create `SendHouseholdInvitationAction` — generates signed token, sends email
- [ ] Create `AcceptHouseholdInvitationAction` — validates token, adds user to household
- [ ] Register invitation routes
- [ ] Build invite form in household settings page
- [ ] Write tests for invitation send, accept, and expiry

### 6.3 Per-Member Roles / Permissions

- [ ] Install `spatie/laravel-permission` (or use Laravel Gates)
- [ ] Define roles: `owner`, `member`, `viewer`
- [ ] Apply role checks via Gate policies on resource mutations (create, update, delete)
- [ ] Restrict viewer-role users to read-only API access
- [ ] Add member management UI to household settings (change role, remove member)
- [ ] Write tests for each role's access boundaries

---

## 7. Activity Log / Audit Trail

- [ ] Install `spatie/laravel-activitylog`
- [ ] Add `LogsActivity` trait to `InventoryItem` and `StockLocation` models
- [ ] Configure activity log to record `created`, `updated`, `deleted` events
- [ ] Log `quantity` old/new values explicitly on `InventoryItem` updates
- [ ] Register `GET /api/inventory-items/{id}/activity` endpoint
- [ ] Add activity feed to `InventoryItem` show page
- [ ] Write tests for activity log recording and retrieval

---

## 8. Reporting & Analytics

### 8.1 Inventory Value Report

- [ ] Create `InventoryValueReportAction` — returns sum of `unit_price × quantity` per location and overall
- [ ] Register `GET /api/reports/inventory-value` route
- [ ] Build inventory value summary component on the dashboard or a dedicated reports page

### 8.2 Expiry Report

- [ ] Create `ExpiryReportAction` — returns items grouped by urgency: expired, <7 days, <30 days, >30 days
- [ ] Register `GET /api/reports/expiry` route
- [ ] Build expiry report page with urgency-coloured groupings
- [ ] Add CSV export for the report

### 8.3 Shopping Spend Report

- [ ] Create `ShoppingSpendReportAction` — totals estimated and actual spend across completed lists, grouped by month/category
- [ ] Register `GET /api/reports/shopping-spend` route (requires `actual_price` from 5.3)
- [ ] Build spend report page with monthly breakdown

### 8.4 Stock Movement Report

- [ ] Create `StockMovementReportAction` — uses activity log (requires section 7) to chart quantity changes over time for a given item
- [ ] Register `GET /api/reports/stock-movement/{itemId}` route
- [ ] Build quantity-over-time chart on the item show page

---

## 9. Mobile / Progressive Web App (PWA)

### 9.1 Web App Manifest + Service Worker

- [ ] Install `vite-plugin-pwa`
- [ ] Configure manifest (name, icons, theme colour, start URL)
- [ ] Configure service worker with cache-first strategy for Vue shell
- [ ] Configure background sync for offline write operations
- [ ] Test install prompt on iOS and Android

### 9.2 Mobile-Optimised UI

- [ ] Audit and increase tap target sizes on interactive elements (minimum 44×44px)
- [ ] Implement swipe-to-delete on shopping list items and inventory list rows
- [ ] Add bottom navigation bar for small screens (replacing or supplementing top nav)
- [ ] Ensure barcode scanning camera UI (from 2.2) is accessible on mobile
- [ ] Test full flows on iOS Safari and Android Chrome

---

## 10. Import / Export

### 10.1 CSV Import for Inventory Items

- [ ] Create `ImportInventoryItemsJob` queued job that validates rows and reports per-row errors
- [ ] Create `ImportInventoryItemsAction` — accepts uploaded file, dispatches job
- [ ] Register `POST /api/inventory-items/import` route
- [ ] Provide a downloadable CSV template with correct column headers
- [ ] Add file upload UI with import progress and error summary
- [ ] Write tests for valid import, partial errors, and invalid file handling

### 10.2 CSV / PDF Export

- [ ] Create `ExportInventoryItemsAction` returning a CSV stream
- [ ] Create `ExportShoppingListAction` returning a formatted PDF (using `barryvdh/laravel-dompdf` or similar)
- [ ] Register export routes
- [ ] Add export buttons to inventory index and shopping list show pages
- [ ] Write tests for export output format

### 10.3 Integration with Grocery / Product APIs

- [ ] Integrate Open Food Facts API (`https://world.openfoodfacts.org/api/v2`) for food items (barcode lookup)
- [ ] Integrate UPC Item DB or similar for non-food products
- [ ] Abstract behind a `ProductLookupService` so the data source is swappable
- [ ] Cache API responses to reduce external requests (Laravel cache with 24h TTL)
- [ ] Write tests against mocked API responses

---

## 11. Search & Discovery

### 11.1 Global Search

- [ ] Install and configure Laravel Scout with the database driver
- [ ] Make `InventoryItem`, `StockLocation`, and `ShoppingList` searchable (implement `Searchable` trait)
- [ ] Create `GlobalSearchAction` that queries all three models and returns results grouped by type
- [ ] Register `GET /api/search?q=` route
- [ ] Build global search UI — command-palette style overlay (keyboard shortcut to open)
- [ ] Write tests for search results and empty states

### 11.2 "Where Is It?" Lookup

- [ ] Add a dedicated "Where is it?" page that focuses the global search on inventory items and shows location + quantity prominently
- [ ] Support deep-linking (`/find?q=drill`) so the URL is shareable
- [ ] Show a "Not found" state with a prompt to add the item

---

## 12. Code Quality & Infrastructure

### 12.1 API Resources / Response Transformation

Currently actions return Eloquent models directly. API Resources decouple the response shape and allow computed fields.

- [ ] Create `InventoryItemResource` with computed `is_low_stock` and `days_until_expiry` fields
- [ ] Create `StockLocationResource`
- [ ] Create `ShoppingListResource` and `ShoppingListItemResource`
- [ ] Create `ShoppingCategoryResource`
- [ ] Update all action `asController()` methods and routes to wrap responses in Resources
- [ ] Ensure camelCase field names are consistent across all resources
- [ ] Update frontend Pinia stores to use the new response shapes

### 12.2 API Versioning

- [ ] Prefix all routes in `routes/api.php` with `/v1/`
- [ ] Update Vue Pinia stores and API base URL config accordingly
- [ ] Document the versioning strategy in the project README

### 12.3 Rate Limiting

- [ ] Define named rate limiters in `AppServiceProvider` (e.g. `api` — 60/min per user, `mutations` — 30/min per user)
- [ ] Apply `throttle:api` middleware to all GET routes
- [ ] Apply `throttle:mutations` to all POST/PUT/DELETE routes
- [ ] Return standard 429 responses with `Retry-After` header

### 12.4 HTTP-Layer Feature Tests

- [ ] Write feature tests for all inventory item routes using `actingAs` + `getJson`/`postJson`
- [ ] Write feature tests for all stock location routes
- [ ] Write feature tests for authentication edge cases (unauthenticated, wrong user)
- [ ] Integrate HTTP tests into CI alongside existing action tests

### 12.5 Media Orphan Detection

Guard against photos that have been deleted from disk (accidental deletion, storage migration, etc.) while their `Media` DB records still exist — preventing broken image URLs in the UI and stale embeddings in the vector index.

- [ ] Create `DetectOrphanedMediaCommand` Artisan command (`php artisan media:detect-orphans`)
- [ ] Command queries all `Media` records for the `photos` collection and checks `Storage::exists()` for each underlying file
- [ ] For each orphaned record (file missing, DB row present): delete the `Media` record and null `photo_embedding` on the parent `InventoryItem`
- [ ] Add `--dry-run` flag that reports orphan count without making changes
- [ ] Register command in `routes/console.php` on a weekly schedule
- [ ] Write tests for both the detection logic and the `--dry-run` path

### 12.6 OpenAPI / Swagger Documentation

- [ ] Install `dedoc/scramble` (compatible with the `AsAction` pattern)
- [ ] Configure Scramble to scan `routes/api.php`
- [ ] Annotate actions with response types where inference is insufficient
- [ ] Verify generated docs at `/docs/api` and include in project README

---

## 13. Image Embeddings & Visual Search (RAG / Vector Search)

> **Depends on:** Section 2.1 (Item Photos) must be implemented first.
>
> **Chosen approach:** Self-hosted CLIP for free image embeddings + PostgreSQL + pgvector for storage.
> PostgreSQL migration is a prerequisite for this section — see 13.1.

### Background

The goal is to find inventory items by visual similarity — upload or snap a photo and get back matching items, or type a plain-English description like "black cordless drill" and have it find the right item even if it was saved as "Makita 18V." This works through **vector embeddings**: a model converts an image (or text) into a list of floating-point numbers representing its semantic meaning. Items with similar meanings land close together in that vector space, and finding the nearest neighbours to a query vector is the search.

The key is using a **CLIP-style multimodal model**, which maps both images *and* text into the same vector space. This means you can embed an item photo at upload time, and later query it with either another photo *or* a text description — both work against the same stored vectors.

**Embedding pipeline (same regardless of storage backend):**
```
Upload photo
  → Send to CLIP model → get back float[] vector (512 dimensions)
  → Store vector alongside InventoryItem
  → On search: embed query (image or text) → find nearest vectors → return items
```

---

### 13.1 Migrate from SQLite to PostgreSQL

This is a prerequisite for pgvector. It's also the right long-term call for a production app — PostgreSQL has better concurrency, proper foreign key enforcement, and is supported by every managed host.

- [ ] Provision a PostgreSQL database (Supabase free tier includes pgvector pre-installed and is the easiest path)
- [ ] Update `.env`: set `DB_CONNECTION=pgsql` and fill in `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- [ ] Run `php artisan migrate:fresh` against the new database (no data to migrate at this stage unless in production)
- [ ] Enable the pgvector extension: `CREATE EXTENSION IF NOT EXISTS vector;` (Supabase does this automatically; self-hosted Postgres needs `apt install postgresql-16-pgvector` or similar)
- [ ] Verify all existing tests still pass: `php artisan test --compact`

> **Why not SQLite + sqlite-vec?** `sqlite-vec` is viable at small scale (thousands of items) but requires loading a native C extension in PHP (`sqlite3_load_extension`), which is often disabled in shared hosting environments. pgvector is a first-class SQL extension with better Eloquent integration and is the industry standard for this pattern. Switching to Postgres is worth doing anyway before data volume grows.

---

### 13.2 Set Up CLIP for Free Image Embeddings

CLIP (Contrastive Language–Image Pre-training) is an open-source model from OpenAI that encodes both images and text into a shared 512-dimensional vector space. It can be self-hosted for free using Python + `transformers`, or called via Replicate's API at ~$0.0001/image.

**Option A — Self-hosted via a sidecar Python service (free, recommended):**

- [ ] Create a small Python FastAPI service (`clip-service/main.py`) that loads the `openai/clip-vit-base-patch32` model via HuggingFace `transformers` and exposes two endpoints:
  - `POST /embed/image` — accepts an image file, returns `{ "embedding": float[] }`
  - `POST /embed/text` — accepts `{ "text": "..." }`, returns `{ "embedding": float[] }`
- [ ] Add `clip-service/requirements.txt`: `fastapi`, `uvicorn`, `transformers`, `torch`, `Pillow`
- [ ] Add `CLIP_SERVICE_URL=http://localhost:8001` to `.env`
- [ ] Create a `ClipEmbeddingService` Laravel service class that calls this local HTTP service

**Option B — Replicate API (no setup, pay-per-use):**

- [ ] Add `REPLICATE_API_TOKEN` to `.env`
- [ ] Use model `andreasjansson/clip-features` on Replicate
- [ ] Create the same `ClipEmbeddingService` but call Replicate instead of localhost

The `ClipEmbeddingService` interface is the same either way — the rest of the code doesn't care which backend is used:

```php
// app/Services/ClipEmbeddingService.php

class ClipEmbeddingService
{
    public function embedImage(string $imagePath): array  // returns float[]
    {
        $response = Http::attach('image', file_get_contents($imagePath), 'image.jpg')
            ->post(config('services.clip.url') . '/embed/image');

        return $response->json('embedding');
    }

    public function embedText(string $text): array  // returns float[]
    {
        $response = Http::post(config('services.clip.url') . '/embed/text', [
            'text' => $text,
        ]);

        return $response->json('embedding');
    }
}
```

---

### 13.3 Add Embedding Storage to InventoryItem

- [ ] Install `pgvector/pgvector-php`: `composer require pgvector/pgvector`
- [ ] Create a migration to add the vector column:
  ```php
  // In the migration up() method:
  DB::statement('ALTER TABLE inventory_items ADD COLUMN photo_embedding vector(512)');
  DB::statement('CREATE INDEX ON inventory_items USING hnsw (photo_embedding vector_cosine_ops)');
  ```
  The HNSW index makes nearest-neighbour queries fast even as the table grows.
- [ ] Add `HasNeighbors` trait and cast to the `InventoryItem` model:
  ```php
  use Pgvector\Laravel\HasNeighbors;
  use Pgvector\Laravel\Vector;

  class InventoryItem extends Model
  {
      use HasNeighbors;

      protected $casts = ['photo_embedding' => Vector::class];
  }
  ```
- [ ] Write a migration test to confirm the column and index are created correctly

---

### 13.4 Generate & Store Embeddings on Photo Upload

This hooks into section 2.1's `AddInventoryItemPhotoAction`. After storing the photo via `spatie/laravel-medialibrary`, dispatch a queued job to generate and persist the embedding asynchronously (CLIP inference can take 1–3 seconds).

- [ ] Create `GenerateItemEmbeddingJob` queued job:
  ```php
  class GenerateItemEmbeddingJob implements ShouldQueue
  {
      public function __construct(public InventoryItem $item) {}

      public function handle(ClipEmbeddingService $clip): void
      {
          $photoPath = $this->item->getFirstMediaPath('photos');
          if (!$photoPath) { return; }

          $embedding = $clip->embedImage($photoPath);
          $this->item->update(['photo_embedding' => $embedding]);
      }
  }
  ```
- [ ] Dispatch `GenerateItemEmbeddingJob::dispatch($item)` at the end of `AddInventoryItemPhotoAction::handle()`
- [ ] Handle the case where an item has multiple photos — use the first/primary photo for the embedding, or average the embeddings across all photos
- [ ] In `DeleteInventoryItemPhotoAction::handle()`, after deleting the media record, null the `photo_embedding` column if the deleted photo was the item's only (or primary) photo — a stale embedding pointing at a non-existent file is worse than no embedding at all
- [ ] Write a test that mocks `ClipEmbeddingService` and asserts the embedding is stored after photo upload
- [ ] Write a test that asserts `photo_embedding` is nulled when the last photo is deleted

---

### 13.5 Visual Search Action & API Endpoint

- [ ] Create `SearchItemsByImageAction`:
  ```php
  class SearchItemsByImageAction
  {
      use AsAction;

      public function handle(User $user, array $queryEmbedding, int $limit = 10): Collection
      {
          return InventoryItem::query()
              ->where('user_id', $user->id)
              ->whereNotNull('photo_embedding')
              ->nearestNeighbors('photo_embedding', $queryEmbedding, Distance::Cosine)
              ->take($limit)
              ->get();
      }

      public function asController(Request $request): Collection
      {
          $clip = app(ClipEmbeddingService::class);

          // Accept either an uploaded image or a text query
          if ($request->hasFile('image')) {
              $embedding = $clip->embedImage($request->file('image')->getPathname());
          } else {
              $embedding = $clip->embedText($request->input('q'));
          }

          return $this->handle($request->user(), $embedding);
      }

      public function rules(): array
      {
          return [
              'image' => 'nullable|image|max:10240',
              'q'     => 'nullable|string|max:500',
          ];
      }
  }
  ```
- [ ] Register route: `POST /api/inventory-items/search/visual`
  - Accepts either `image` (file upload) or `q` (text string) — not both required
- [ ] Write tests for both image-based and text-based search paths (mock `ClipEmbeddingService`)

---

### 13.6 Frontend — Visual Search UI

- [ ] Add a "Visual Search" tab or toggle to the inventory index page (`inventory/Index.vue`)
- [ ] In visual search mode, show a camera/upload input instead of the text search box
- [ ] On submit, `POST` to `/api/inventory-items/search/visual` with either the file or the text query
- [ ] Display results as a visual grid (photo thumbnails + name + location) rather than the standard table
- [ ] Show a "No visual matches found" state for items without embeddings yet, with a note that embeddings generate after photo upload

---

### 13.7 Backfill Embeddings for Existing Photos

Once the system is live, existing items with photos won't have embeddings yet.

- [ ] Create `php artisan embeddings:backfill` Artisan command that queries all items with photos but no `photo_embedding` and dispatches `GenerateItemEmbeddingJob` for each
- [ ] Add `--dry-run` flag that reports how many items would be processed without dispatching
- [ ] Write a test for the command

---

## 14. MCP Server — AI Agent Interface

The primary goal of this project is to expose the home inventory system to AI agents via the [Model Context Protocol](https://laravel.com/docs/12.x/mcp). The official `laravel/mcp` package makes this straightforward: tools are callable actions; resources are readable data by URI. Authentication is handled by Sanctum — the same tokens the Vue frontend uses.

> **Depends on:** Section 1 (all routes wired and working — done). Section 12.1 (API Resources) is desirable but not a hard blocker; tools can return Eloquent models directly and be updated later to use Resources.

### Transport & Auth

The server will be registered in `routes/ai.php` (a new file the package creates, analogous to `routes/api.php`) and protected with the existing Sanctum middleware. MCP clients connect via HTTP (SSE transport) using `Authorization: Bearer <token>` — no new auth infrastructure required.

```php
// routes/ai.php
Mcp::web('/mcp', HomeInventoryServer::class)->middleware(['auth:sanctum']);
```

### Validation Strategy

Tool `handle()` methods should **not** repeat validation rules. Instead, resolve the Action from the container and call `$request->validate($action->rules())` before delegating to `handle()`. This reuses the exact same rules already defined on each Action without duplication:

```php
public function handle(Request $request): mixed
{
    $action = app(CreateItem::class);
    $request->validate($action->rules());

    return $action->handle($request->user(), $request->validated());
}
```

Rules that use `auth()->id()` (e.g. the inventory item ownership check in `AddInventoryItemToShoppingListAction`) work correctly because Sanctum still populates the auth context on the MCP route.

---

### 14.1 Install & Bootstrap

- [x] Install the package: `composer require laravel/mcp`
- [x] Run `php artisan make:mcp-server HomeInventoryServer` — generates `app/Mcp/Servers/HomeInventoryServer.php` and `routes/ai.php`
- [x] Register Sanctum middleware on the server in `routes/ai.php`
- [x] Add clear `$instructions` to `HomeInventoryServer` describing the system to the agent: what the inventory is, how locations work, how shopping lists relate to inventory items, and a note to always confirm before any destructive operation

---

### 14.2 Resources (Read-Only Data)

Resources expose data by URI for the agent to read without side effects. Each resource class resolves the authenticated user from the request and delegates to the existing Actions.

- [x] Create `InventoryItemsResource` — URI `inventory://items` — calls `LoadItems` with the authenticated user, returns paginated items with name, quantity, location, unit, and expiry
- [x] Create `InventoryItemResource` — URI `inventory://items/{id}` — calls `LoadItem`, returns full item detail
- [x] Create `StockLocationsResource` — URI `inventory://locations` — calls `LoadStockLocations`, returns all locations with item counts
- [x] Create `DashboardResource` — URI `inventory://dashboard` — calls `GetDashboardSummaryAction`, gives the agent a snapshot of low stock, expiring items, and totals — the most useful starting point for any agent session
- [x] Create `ShoppingListsResource` — URI `shopping://lists` — calls `GetShoppingListsByUserAction`
- [x] Create `ShoppingListResource` — URI `shopping://lists/{id}` — calls `GetShoppingListAction` with items eager-loaded
- [x] Write tests for each resource (assert correct user scoping and expected response shape)

---

### 14.3 Tools (Actions the Agent Can Take)

Tools are the writable side. Each tool calls `$request->validate($action->rules())` before delegating to the corresponding Action's `handle()` — no duplicate validation logic.

**Inventory tools:**

- [x] Create `AddInventoryItemTool` — wraps `CreateItem` — schema fields: `name` (required), `quantity` (required), `stock_location_id` (required), `unit`, `reorder_point`, `expiration_date`, `description`
- [x] Create `UpdateInventoryItemTool` — wraps `UpdateItem` — schema fields match `UpdateItem` rules; tool receives `inventory_item_id` as a required param to identify the record
- [x] Create `AdjustItemQuantityTool` — a focused tool for the most common agentic operation (incrementing/decrementing stock); accepts `inventory_item_id` and `adjustment` (positive or negative integer) and applies it to current quantity — simpler than asking the agent to send the full update payload
- [x] Create `FindLowStockItemsTool` — accepts optional `threshold` override; queries items where `quantity <= reorder_point` (or `min_stock_level` as fallback) for the user; returns as a tool rather than resource because the threshold is parameterised per-call

**Shopping list tools:**

- [x] Create `CreateShoppingListTool` — wraps `CreateShoppingListAction` — schema: `name` (required), `notes`, `shopping_date`
- [x] Create `AddItemToShoppingListTool` — wraps `CreateShoppingListItemAction` for free-form items and `AddInventoryItemToShoppingListAction` for linked items; accepts `shopping_list_id` (required), `name` OR `inventory_item_id` (one required), `quantity` (required) — tool chooses the right action based on which is provided
- [x] Create `MarkShoppingListCompleteTool` — wraps `UpdateShoppingListAction` with `is_completed: true`; separate from a general update tool to make the agent's intent explicit and easy to trigger with natural language

**Intentionally omitted from v1:**

- Delete item, delete shopping list — omit until a confirmation/undo pattern is established; the `$instructions` on the server should explain this to the agent

- [x] Write tests for each tool (assert correct delegation to Action, correct user scoping, and that validation errors surface cleanly)

---

### 14.4 Prompts

Prompts are reusable conversation starters surfaced to the agent. Register in `HomeInventoryServer::$prompts`.

- [x] Create `WhatDoINeedToBuyPrompt` — instructs the agent to read `inventory://dashboard`, identify low-stock and expiring items, then offer to create a new shopping list from them
- [x] Create `WhereIsMyItemPrompt` — instructs the agent to search inventory items by name and return the item's location and current quantity
- [x] Create `AddGroceriesPrompt` — a guided multi-step flow: the agent asks the user what they bought, maps each to an existing inventory item or creates a new one, and calls `AdjustItemQuantityTool` for each

---

### 14.5 Directory Structure

```
app/Mcp/
  Servers/
    HomeInventoryServer.php
  Tools/
    AddInventoryItemTool.php
    UpdateInventoryItemTool.php
    AdjustItemQuantityTool.php
    FindLowStockItemsTool.php
    CreateShoppingListTool.php
    AddItemToShoppingListTool.php
    MarkShoppingListCompleteTool.php
  Resources/
    InventoryItemsResource.php
    InventoryItemResource.php
    StockLocationsResource.php
    DashboardResource.php
    ShoppingListsResource.php
    ShoppingListResource.php
  Prompts/
    WhatDoINeedToBuyPrompt.php
    WhereIsMyItemPrompt.php
    AddGroceriesPrompt.php
```

---

## Priority Summary

| Priority | Section | Status |
|----------|---------|--------|
| 🔴 High | 1.1 Shopping list API routes | [x] |
| 🔴 High | 1.2 Shopping category actions + routes | [x] |
| 🔴 High | 1.3 Shopping list frontend | [x] |
| 🔴 High | 1.4 Dashboard overview | [x] |
| 🔴 High | 14. MCP server — AI agent interface | [x] |
| 🟡 Medium | 4.1–4.4 Notifications + preferences | [x] |
| 🟡 Medium | 5.1 Auto-replenish on list completion | [ ] |
| 🟡 Medium | 12.1 API Resources | [ ] |
| 🟡 Medium | 2.1 Item photos | [ ] |
| 🟡 Medium | 11.1 Global search | [ ] |
| 🟢 Low | 2.2 Barcode scanning | [ ] |
| 🟢 Low | 6. Multi-user households | [ ] |
| 🟢 Low | 9. PWA / mobile UX | [ ] |
| 🟢 Low | 7. Activity log | [ ] |
| 🟢 Low | 8. Reporting | [ ] |
| 🟢 Low | 10. Import / Export | [ ] |
| 🟢 Low | 13. Image embeddings & visual search | [ ] |
