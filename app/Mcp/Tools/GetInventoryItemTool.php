<?php

namespace App\Mcp\Tools;

use App\Actions\InventoryItem\LoadItem;
use App\Models\InventoryItem;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetInventoryItemTool extends Tool
{
    protected string $name = 'get_inventory_item';

    protected string $description = 'Get full details for a single inventory item by ID, including its stock location and all fields.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The ID of the inventory item to retrieve.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $item = InventoryItem::where('user_id', $request->user()->id)
            ->with('stockLocation')
            ->findOrFail($data['id']);

        return Response::json(
            app(LoadItem::class)->handle($request->user(), $item)->toArray()
        );
    }
}
