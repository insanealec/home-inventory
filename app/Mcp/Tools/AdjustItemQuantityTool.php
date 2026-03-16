<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class AdjustItemQuantityTool extends Tool
{
    protected string $name = 'adjust_item_quantity';

    protected string $description = 'Increase or decrease the quantity of an inventory item by a given amount. Prefer this over update_inventory_item when only the quantity is changing — it is more precise and prevents accidental overwrites.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'inventory_item_id' => $schema->integer()
                ->required()
                ->description('The ID of the inventory item to adjust. Use inventory://items to find item IDs.'),

            'adjustment' => $schema->integer()
                ->required()
                ->description('The amount to add (positive) or subtract (negative) from the current quantity. E.g. -2 means "used 2", +5 means "restocked 5".'),

            'reason' => $schema->string()
                ->nullable()
                ->description('Optional reason for the adjustment, for context (e.g. "used for dinner", "restocked from Costco")'),
        ];
    }

    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'inventory_item_id' => 'required|integer',
            'adjustment' => 'required|integer',
            'reason' => 'nullable|string|max:255',
        ]);

        $item = $request->user()
            ->inventoryItems()
            ->findOrFail($data['inventory_item_id']);

        $previousQuantity = $item->quantity;
        $newQuantity = max(0, $previousQuantity + $data['adjustment']);
        $item->update(['quantity' => $newQuantity]);

        return Response::json([
            'id' => $item->id,
            'name' => $item->name,
            'previous_quantity' => $previousQuantity,
            'adjustment' => $data['adjustment'],
            'new_quantity' => $newQuantity,
        ]);
    }
}
