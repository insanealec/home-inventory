<?php

namespace App\Mcp\Resources;

use App\Actions\InventoryItem\LoadItem;
use App\Models\InventoryItem;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class InventoryItemResource extends Resource
{
    protected string $uri = 'inventory://items/{id}';

    protected string $name = 'Inventory Item';

    protected string $description = 'Full detail for a single inventory item by ID, including its stock location and all fields.';

    public function handle(Request $request): Response
    {
        $item = InventoryItem::where('user_id', $request->user()->id)
            ->with('stockLocation')
            ->findOrFail($request->integer('id'));

        return Response::json(
            app(LoadItem::class)->handle($request->user(), $item)->toArray()
        );
    }
}
