<?php

namespace App\Mcp\Resources;

use App\Actions\InventoryItem\LoadItems;
use App\Data\InventoryItem\LoadItemsFilters;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class InventoryItemsResource extends Resource
{
    protected string $uri = 'inventory://items';

    protected string $name = 'Inventory Items';

    protected string $description = 'Paginated list of all inventory items belonging to the user, including name, quantity, unit, location, reorder point, and expiration date. Supports optional search and location filtering via query parameters.';

    public function handle(Request $request): Response
    {
        $filters = new LoadItemsFilters(
            search: $request->get('search'),
            stockLocationId: $request->integer('stock_location_id') ?: null,
            sortBy: $request->get('sort_by', 'name'),
            sortDirection: $request->get('sort_direction', 'asc'),
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 50),
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
