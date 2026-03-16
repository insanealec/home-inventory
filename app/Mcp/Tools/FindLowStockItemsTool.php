<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class FindLowStockItemsTool extends Tool
{
    protected string $name = 'find_low_stock_items';

    protected string $description = 'Find inventory items that are running low. Returns items where the current quantity is at or below their configured reorder point, or below a custom threshold you specify. Use this before creating a shopping list to know what needs restocking.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'threshold' => $schema->integer()
                ->nullable()
                ->min(0)
                ->description('Optional quantity threshold. If provided, returns all items with quantity at or below this value — regardless of their individual reorder point. If omitted, uses each item\'s configured reorder_point.'),

            'limit' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Maximum number of results to return. Defaults to 20.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'threshold' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1',
        ]);

        $limit = $data['limit'] ?? 20;
        $query = $request->user()->inventoryItems()->with('stockLocation');

        if (isset($data['threshold'])) {
            $query->where('quantity', '<=', $data['threshold']);
        } else {
            $query->where('reorder_point', '>', 0)
                ->whereColumn('quantity', '<=', 'reorder_point');
        }

        $items = $query->orderBy('quantity')
            ->limit($limit)
            ->get(['id', 'name', 'quantity', 'reorder_point', 'unit', 'stock_location_id']);

        return Response::json([
            'count' => $items->count(),
            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'reorder_point' => $item->reorder_point,
                'unit' => $item->unit,
                'location' => $item->stockLocation?->name,
            ])->all(),
        ]);
    }
}
