<?php

namespace App\Mcp\Tools;

use App\Actions\InventoryItem\UpdateItem;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UpdateInventoryItemTool extends Tool
{
    protected string $name = 'update_inventory_item';

    protected string $description = 'Update the details of an existing inventory item such as its name, description, location, or position. To change the quantity use adjust_item_quantity instead.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->required()
                ->description('The ID of the inventory item to update. Use the inventory://items resource to find item IDs.'),

            'name' => $schema->string()
                ->required()
                ->description('The item name'),

            'quantity' => $schema->integer()
                ->required()
                ->min(0)
                ->description('Current quantity on hand'),

            'sku' => $schema->string()
                ->nullable()
                ->description('Optional SKU or barcode identifier'),

            'description' => $schema->string()
                ->nullable()
                ->description('Optional notes or description'),

            'stock_location_id' => $schema->integer()
                ->nullable()
                ->description('ID of the stock location. Use inventory://locations to find location IDs.'),

            'position' => $schema->string()
                ->nullable()
                ->description('Specific position within the location (e.g. "Top shelf")'),
        ];
    }

    public function handle(Request $request, UpdateItem $action): Response
    {
        $data = $request->validate($action->rules());

        $item = $request->user()
            ->inventoryItems()
            ->findOrFail($data['id']);

        return Response::json($action->handle($item, $data)?->toArray());
    }
}
