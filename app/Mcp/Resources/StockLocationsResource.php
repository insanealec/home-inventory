<?php

namespace App\Mcp\Resources;

use App\Actions\StockLocation\LoadStockLocations;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class StockLocationsResource extends Resource
{
    protected string $uri = 'inventory://locations';

    protected string $name = 'Stock Locations';

    protected string $description = 'All storage locations defined by the user (e.g. Kitchen Pantry, Garage Shelf). Each location includes its name and item count. Use location IDs when adding or updating inventory items.';

    public function handle(Request $request): Response
    {
        $paginator = app(LoadStockLocations::class)->handle($request->user(), [
            'sortBy' => 'name',
            'sortDirection' => 'asc',
            'perPage' => 100,
        ]);

        return Response::json([
            'locations' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }
}
