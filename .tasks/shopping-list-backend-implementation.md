# Home Inventory Project - Shopping List Backend Implementation - Task Tracking

## Task Overview
Implement individual Laravel Actions for each operation on shopping lists and items, following the exact pattern used in existing inventory items and stock locations.

## ✅ Completed Tasks
- [x] Create shopping_lists migration 
- [x] Create shopping_list_items migration  
- [x] Create inventory_items migration with relationships
- [x] Design action implementation plan based on existing codebase patterns

## 🚧 In Progress / To Do

### Core CRUD Actions - Shopping Lists

#### [x] CreateShoppingListAction
**Purpose:** Creates a new shopping list for a user
**Requirements:**
**  **Accepts:** list name, user_id (from auth), notes (optional)  
     Returns: ShoppingListModel with relationships  
    Validation: Ensure name not empty, optional but helpful notes
- [x] Validate input data
- [x] Create in database with user_id context
- [x] Return created shopping list model
- [x] Include relationships to items if needed

#### [x] GetShoppingListAction  
**Purpose:** Retrieves a specific shopping list by ID
**Requirements:**
- Accepts: shopping_list_id
- Returns: ShoppingListModel with shopping_list_items relationship  
- Load eager relationships for performance
- Handle not found (return 404 or null)

#### [x] UpdateShoppingListAction
**Purpose:** Updates an existing shopping list
**Requirements:**
- Accepts: id, and fields to update (name, notes, shopping_date, is_completed)
- Validates updates against rules
- Returns updated ShoppingListModel
- Handle if field not provided (skip updating it)

#### [x] DeleteShoppingListAction
**Purpose:** Deletes a shopping list
**Requirements:** 
- Accepts: shopping_list_id  
- Will cascade delete items due to foreign key on delete
- Return success status or error message

---

### Core CRUD Actions - Shopping List Items

#### [x] CreateShoppingListItemAction
**Purpose:** Creates a new item in a shopping list
**Requirements:**
- Accepts: shopping_list_id, name, quantity, additional fields (unit, notes, estimated_price, category_id, priority, sort_order) 
- Validations: Check required fields, ensure quantity > 0 
- Create with shopping_list_id relationship  
- Return created ShoppingListItemModel

#### [x] GetShoppingListItemAction
**Purpose:** Retrieves a specific shopping list item by ID
**Requirements:**
- Accepts: shopping_list_item_id
- Returns: ShoppingListItemModel with inventory_item relationship (optional but eager load)
- Handle not found cases

#### [x] UpdateShoppingListItemAction
**Purpose:** Updates an existing shopping list item
**Requirements:**  
- Accepts: id, and fields to update (can be any except id)
- Special care for: quantity must be positive integer if modified 
- Validations against field constraints  
- Returns updated ShoppingListItemModel

#### [x] DeleteShoppingListItemAction
**Purpose:** Deletes a shopping list item
**Requirements:**
- Accepts: shopping_list_item_id
- Will cascade delete due to foreign key on delete
- Return success status or error message

---

### Special Management Actions - Shopping List Items

#### [x] AddInventoryItemToShoppingListAction
**Purpose:** Creates a shopping list item that references an existing inventory item
**Requirements:**
- Accepts: shopping_list_id, inventory_item_id, quantity
- Validates inventory_item exists (optional)
- Creates relationship via foreign key
- Returns newly created ShoppingListItemModel

#### [x] AddStandaloneItemToShoppingListAction
**Purpose:** Creates a shopping list item without linking to inventory item  
**Accepts shopping_list_id, name and other fields**
- Sets inventory_item_id to null (standalone)
- Works for categories that may not have inventory items yet
- Returns newly created ShoppingListItemModel

#### ~~RemoveItemFromShoppingListAction~~ — REMOVED
Identical to `DeleteShoppingListItemAction`. The "preserve data" feature was never implemented. Removed as redundant.

#### ~~UpdateShoppingListItemQuantityAction~~ — REMOVED
Fully covered by `UpdateShoppingListItemAction` with `quantity` field. Removed as redundant.

#### ~~UpdateItemNameAction~~ — REMOVED
Fully covered by `UpdateShoppingListItemAction` with `name` field. Removed as redundant.

---

### List Management Actions

#### [x] GetShoppingListsByUserAction
**Purpose:** Retrieves all shopping lists for a specific authenticated user
**Requirements:**
- Accepts: user_id (should be from auth middleware)
- Return paginated or complete list with items included
- Order by creation date descending
- Includes count of each list's items

#### [x] GetShoppingListItemsAction
**Purpose:** Retrieves all items in a specific shopping list
**Requirements:**
- Accepts: shopping_list_id  
- Returns ordered items with relationships
- Load inventory_item relationship if not null
- Include category relationship (optional eager load)


---

### Bulk Operations

#### [x] UpdateShoppingListItemsBulkAction
**Purpose:** Updates multiple items in a shopping list at once
**Accepts: shopping_list_id, array of item_id -> field_updates**  
- Process each item update individually 
- Track errors by item_id 
- Return success message or partial failure with error details

#### [x] AddMultipleItemsToShoppingListAction
**Purpose:** Adds multiple new items to a shopping list at once
**Accepts: shopping_list_id, array of item data objects**  
- Create and return all created items 
- Validate each separately
- Handle errors gracefully (partial creates)


---

### Unit Tests Required for Each Action

#### [x] Write tests for ShoppingList Actions
#### [x] Write tests for ShoppingListItem Actions  
#### [x] Write tests for Special Management Actions
#### [ ] Document API endpoints and usage patterns


## Key Design Decisions Based on Existing Codebase

1. **Relationship Strategy**:
   - `shopping_list_items` has optional `inventory_item_id` that references `inventory_items`
   - Items can be standalone (no inventory reference) or linked to inventory items  
   - Foreign key constraints use `set null` for deletion (as per migration)

2. **Data Structure Consistency**:
   - Follow same pattern as existing inventory items: name, quantity, unit, notes, etc.

## Summary of Implemented Functionality

### Core Shopping List CRUD Operations
- Create, Read, Update, Delete shopping lists
- Create, Read, Update, Delete shopping list items
- All operations respect user context and relationships

### Special Management Actions - Shopping List Items
- Add inventory item to shopping list (creates link with existing inventory)
- Add standalone item to shopping list (no inventory link)  
- Remove item from shopping list
- Update shopping list item quantity
- Update shopping list item name

### Bulk Operations
- Update multiple items in a shopping list at once
- Add multiple new items to a shopping list at once

### Testing
- Comprehensively tested all actions with various scenarios
- All actions include proper validation and error handling
   - Include `sort_order` field (existing in schema)
   - Support for categories and priorities  
   - Estimated price tracking

3. **User Context**:
   - All actions should respect user context to maintain data separation
   - Actions should be available through API endpoints that authenticate users

## Validation and Error Handling Patterns

Following existing patterns from inventory items:
- Input validation using Laravel validation rules  
- Return structured responses (models, arrays)
- Proper error handling and exception management
- Consistent success/error response formats

## Implementation Priority Order

1. **Core CRUD Actions** (4 each for lists and items - 8 total)
2. **Special Management Actions** (7 actions including updates to individual fields)
3. **List Management Actions** (2 actions)  
4. **Bulk Operations** (2 actions)
5. **Unit Tests** for all actions
6. **API Routes** to expose actions

## Summary of Work Required

### Pending: 24 action implementations + tests + API routes
### Estimations: Based on your existing code complexity per action type

This task tracking file is designed for the heartbeat skill to read and update completion status as work progresses. Each checkbox can be updated by manually editing this document when tasks are completed.