<?php

namespace App\Mcp\Tools;

use App\Actions\ShoppingList\AddInventoryItemToShoppingListAction;
use App\Actions\ShoppingList\AddStandaloneItemToShoppingListAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class AddItemToShoppingListTool extends Tool
{
    protected string $name = 'add_item_to_shopping_list';

    protected string $description = 'Add an item to a shopping list. If the item exists in your inventory, provide inventory_item_id to link it — checking it off later can then update your stock. Otherwise, provide a name for a standalone entry.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'shopping_list_id' => $schema->integer()
                ->required()
                ->description('The ID of the shopping list to add the item to. Use shopping://lists to find list IDs.'),

            'inventory_item_id' => $schema->integer()
                ->nullable()
                ->description('ID of an existing inventory item to link. Provide this OR name — not both. Use inventory://items to find item IDs.'),

            'name' => $schema->string()
                ->nullable()
                ->description('Name for a standalone (non-inventory) item. Provide this OR inventory_item_id — not both.'),

            'quantity' => $schema->integer()
                ->required()
                ->min(1)
                ->description('How many units to buy'),

            'unit' => $schema->string()
                ->nullable()
                ->description('Unit of measurement (e.g. "kg", "bottles", "cans")'),

            'notes' => $schema->string()
                ->nullable()
                ->description('Optional notes for this item (e.g. "get the organic one")'),

            'estimated_price' => $schema->number()
                ->nullable()
                ->description('Optional estimated price per unit'),
        ];
    }

    public function handle(
        Request $request,
        AddInventoryItemToShoppingListAction $inventoryAction,
        AddStandaloneItemToShoppingListAction $standaloneAction,
    ): Response {
        $data = $request->validate([
            'shopping_list_id' => 'required|integer',
            'inventory_item_id' => 'nullable|integer',
            'name' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'estimated_price' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $shoppingListId = $data['shopping_list_id'];

        if (! empty($data['inventory_item_id'])) {
            $item = $inventoryAction->handle(
                $user,
                $shoppingListId,
                $data['inventory_item_id'],
                $data['quantity'],
            );
        } else {
            abort_if(empty($data['name']), 422, 'Either inventory_item_id or name must be provided.');

            $item = $standaloneAction->handle($user, $shoppingListId, [
                'name' => $data['name'],
                'quantity' => $data['quantity'],
                'unit' => $data['unit'] ?? null,
                'notes' => $data['notes'] ?? null,
                'estimated_price' => $data['estimated_price'] ?? null,
            ]);
        }

        return Response::json($item->toArray());
    }
}
