<?php

namespace App\Mcp\Tools;

use App\Actions\ShoppingList\CreateShoppingListAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateShoppingListTool extends Tool
{
    protected string $name = 'create_shopping_list';

    protected string $description = 'Create a new shopping list. After creating the list, use add_item_to_shopping_list to populate it with items.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()
                ->required()
                ->description('Name for the shopping list (e.g. "Weekly Groceries", "Costco Run")'),

            'notes' => $schema->string()
                ->nullable()
                ->description('Optional notes or context for the list'),

            'shopping_date' => $schema->string()
                ->nullable()
                ->format('date')
                ->description('Optional planned shopping date in YYYY-MM-DD format'),
        ];
    }

    public function handle(Request $request, CreateShoppingListAction $action): Response
    {
        $data = $request->validate($action->rules());

        return Response::json($action->handle($request->user(), $data)->toArray());
    }
}
