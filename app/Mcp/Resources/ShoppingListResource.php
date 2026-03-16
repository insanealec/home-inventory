<?php

namespace App\Mcp\Resources;

use App\Actions\ShoppingList\GetShoppingListAction;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class ShoppingListResource extends Resource
{
    protected string $uri = 'shopping://lists/{id}';

    protected string $name = 'Shopping List';

    protected string $description = 'Full detail for a single shopping list by ID, including all items with their names, quantities, completion state, and any linked inventory item.';

    public function handle(Request $request): Response
    {
        $list = app(GetShoppingListAction::class)->handle($request->user(), $request->integer('id'));

        return Response::json($list->toArray());
    }
}
