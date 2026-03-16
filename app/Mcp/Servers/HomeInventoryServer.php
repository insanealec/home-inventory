<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\AddGroceriesPrompt;
use App\Mcp\Prompts\WhatDoINeedToBuyPrompt;
use App\Mcp\Prompts\WhereIsMyItemPrompt;
use App\Mcp\Resources\DashboardResource;
use App\Mcp\Resources\InventoryItemResource;
use App\Mcp\Resources\InventoryItemsResource;
use App\Mcp\Resources\ShoppingListResource;
use App\Mcp\Resources\ShoppingListsResource;
use App\Mcp\Resources\StockLocationsResource;
use App\Mcp\Tools\AddInventoryItemTool;
use App\Mcp\Tools\AddItemToShoppingListTool;
use App\Mcp\Tools\AdjustItemQuantityTool;
use App\Mcp\Tools\CreateShoppingListTool;
use App\Mcp\Tools\FindLowStockItemsTool;
use App\Mcp\Tools\GetDashboardTool;
use App\Mcp\Tools\GetInventoryItemsTool;
use App\Mcp\Tools\GetInventoryItemTool;
use App\Mcp\Tools\GetShoppingListsTool;
use App\Mcp\Tools\GetShoppingListTool;
use App\Mcp\Tools\GetStockLocationsTool;
use App\Mcp\Tools\MarkShoppingListCompleteTool;
use App\Mcp\Tools\UpdateInventoryItemTool;
use Laravel\Mcp\Server;

class HomeInventoryServer extends Server
{
    protected string $name = 'Home Inventory';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
# Home Inventory Assistant

You help users manage a home inventory system — tracking physical items stored across locations in their home, and managing shopping lists to replenish stock.

## Key concepts

- **Inventory items** have a name, quantity, unit, location, optional reorder point, and optional expiration date. They are stored in **stock locations** (e.g. "Kitchen Pantry", "Garage Shelf").
- **Shopping lists** contain items to buy. Items on a list can be linked to an inventory item (so checking them off can update stock) or exist as free-form entries.
- The **dashboard** is your best starting point — it surfaces low-stock and expiring items at a glance.

## Guidance

- Before taking any action, read the relevant resource first to understand the current state.
- When adjusting quantities, prefer `adjust_item_quantity` over a full update — it is more precise and less error-prone.
- **Never delete items or lists without explicit confirmation from the user.** Deletion is not available as a tool in this version; ask the user to do this through the app if needed.
- If a user asks "what do I need to buy?", start with the `inventory://dashboard` resource, then offer to create a shopping list from the results.
- If a user asks "where is my X?", search inventory items by name using the `inventory://items` resource with a search filter.
MARKDOWN;

    protected array $tools = [
        // Read tools (resource mirrors for clients that don't support resources/read)
        GetDashboardTool::class,
        GetInventoryItemsTool::class,
        GetInventoryItemTool::class,
        GetStockLocationsTool::class,
        GetShoppingListsTool::class,
        GetShoppingListTool::class,
        // Write tools
        AddInventoryItemTool::class,
        UpdateInventoryItemTool::class,
        AdjustItemQuantityTool::class,
        FindLowStockItemsTool::class,
        CreateShoppingListTool::class,
        AddItemToShoppingListTool::class,
        MarkShoppingListCompleteTool::class,
    ];

    protected array $resources = [
        DashboardResource::class,
        InventoryItemsResource::class,
        InventoryItemResource::class,
        StockLocationsResource::class,
        ShoppingListsResource::class,
        ShoppingListResource::class,
    ];

    protected array $prompts = [
        WhatDoINeedToBuyPrompt::class,
        WhereIsMyItemPrompt::class,
        AddGroceriesPrompt::class,
    ];
}
