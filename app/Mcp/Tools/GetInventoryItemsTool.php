<?php

namespace App\Mcp\Tools;

use App\Actions\InventoryItem\LoadItems;
use App\Data\InventoryItem\LoadItemsFilters;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetInventoryItemsTool extends Tool
{
    protected string $name = 'get_inventory_items';

    protected string $description = 'List inventory items belonging to the user. Supports optional search by name and filtering by stock location. Returns paginated results with item name, quantity, unit, location, reorder point, and expiration date.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'search' => $schema->string()
                ->nullable()
                ->description('Optional search string to filter items by name.'),

            'stock_location_id' => $schema->integer()
                ->nullable()
                ->description('Optional stock location ID to filter items by location.'),

            'page' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Page number for pagination. Defaults to 1.'),

            'per_page' => $schema->integer()
                ->nullable()
                ->min(1)
                ->description('Number of items per page. Defaults to 50.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'search' => 'nullable|string',
            'stock_location_id' => 'nullable|integer',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1',
        ]);

        $filters = new LoadItemsFilters(
            search: $data['search'] ?? null,
            stockLocationId: $data['stock_location_id'] ?? null,
            sortBy: 'name',
            sortDirection: 'asc',
            page: $data['page'] ?? 1,
            perPage: $data['per_page'] ?? 50,
        );

        $paginator = app(LoadItems::class)->handle($request->user(), $filters);

        return Response::json([
            'items' => $paginator->items(),
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
