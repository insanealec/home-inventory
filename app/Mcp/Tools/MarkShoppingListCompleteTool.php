<?php

namespace App\Mcp\Tools;

use App\Actions\ShoppingList\UpdateShoppingListAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class MarkShoppingListCompleteTool extends Tool
{
    protected string $name = 'mark_shopping_list_complete';

    protected string $description = 'Mark a shopping list as complete once the shopping trip is done. This flags the list as finished without deleting it, so it remains available for reference.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'shopping_list_id' => $schema->integer()
                ->required()
                ->description('The ID of the shopping list to mark as complete. Use shopping://lists to find list IDs.'),
        ];
    }

    public function handle(Request $request, UpdateShoppingListAction $action): Response
    {
        $data = $request->validate([
            'shopping_list_id' => 'required|integer',
        ]);

        $list = $action->handle($request->user(), $data['shopping_list_id'], [
            'is_completed' => true,
        ]);

        return Response::json($list->toArray());
    }
}
