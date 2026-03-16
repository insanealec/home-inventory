<?php

namespace App\Mcp\Tools;

use App\Actions\ShoppingList\GetShoppingListAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetShoppingListTool extends Tool
{
    protected string $name = 'get_shopping_list';

    protected string $description = 'Get full details for a single shopping list by ID, including all items with their names, quantities, completion state, and any linked inventory item.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The ID of the shopping list to retrieve.'),
        ];
    }

    public function handle(Request $request): Response
    {
        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $list = app(GetShoppingListAction::class)->handle($request->user(), $data['id']);

        return Response::json($list->toArray());
    }
}
