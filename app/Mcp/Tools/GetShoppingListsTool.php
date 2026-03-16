<?php

namespace App\Mcp\Tools;

use App\Actions\ShoppingList\GetShoppingListsByUserAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetShoppingListsTool extends Tool
{
    protected string $name = 'get_shopping_lists';

    protected string $description = 'List all shopping lists belonging to the user, ordered by most recent first. Each list includes its name, completion state, item count, and shopping date if set.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $paginator = app(GetShoppingListsByUserAction::class)->handle($request->user(), 100);

        return Response::json([
            'lists' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }
}
