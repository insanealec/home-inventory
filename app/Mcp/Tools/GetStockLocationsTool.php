<?php

namespace App\Mcp\Tools;

use App\Actions\StockLocation\LoadStockLocations;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetStockLocationsTool extends Tool
{
    protected string $name = 'get_stock_locations';

    protected string $description = 'List all storage locations defined by the user (e.g. Kitchen Pantry, Garage Shelf). Each location includes its name and item count. Use location IDs when adding or updating inventory items.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

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
