<?php

namespace App\Mcp\Resources;

use App\Actions\ShoppingList\GetShoppingListsByUserAction;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class ShoppingListsResource extends Resource
{
    protected string $uri = 'shopping://lists';

    protected string $name = 'Shopping Lists';

    protected string $description = 'All shopping lists belonging to the user, ordered by most recent first. Each list includes its name, completion state, item count, and shopping date if set.';

    public function handle(Request $request): Response
    {
        $paginator = app(GetShoppingListsByUserAction::class)->handle($request->user(), 100);

        return Response::json([
            'lists' => $paginator->items(),
            'total' => $paginator->total(),
        ]);
    }
}
