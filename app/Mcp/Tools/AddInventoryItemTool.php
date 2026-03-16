<?php

namespace App\Mcp\Tools;

use App\Actions\InventoryItem\CreateItem;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class AddInventoryItemTool extends Tool
{
    protected string $name = 'add_inventory_item';

    protected string $description = 'Add a new item to the home inventory. Use this when the user wants to track a new physical item in their home.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->required()
                ->description('The name of the item (e.g. "Olive Oil", "Batteries AA")'),

            'quantity' => $schema->integer()
                ->required()
                ->min(0)
                ->description('Current quantity on hand'),

            'sku' => $schema->string()
                ->nullable()
                ->description('Optional SKU or barcode identifier'),

            'description' => $schema->string()
                ->nullable()
                ->description('Optional notes or description for the item'),

            'stock_location_id' => $schema->integer()
                ->nullable()
                ->description('ID of the stock location where the item is stored. Use the inventory://locations resource to find location IDs.'),

            'position' => $schema->string()
                ->nullable()
                ->description('Specific position within the location (e.g. "Top shelf", "Left drawer")'),
        ];
    }

    public function handle(Request $request, CreateItem $action): Response
    {
        $data = $request->validate($action->rules());

        return Response::json($action->handle($request->user(), $data)->toArray());
    }
}
