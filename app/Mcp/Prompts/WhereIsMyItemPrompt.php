<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class WhereIsMyItemPrompt extends Prompt
{
    protected string $name = 'where_is_my_item';

    protected string $description = 'Find an item in the home inventory and report where it is stored.';

    public function arguments(): array
    {
        return [
            new Argument(
                name: 'item_name',
                description: 'The name (or partial name) of the item to look for.',
                required: true,
            ),
        ];
    }

    public function handle(Request $request): Response
    {
        $itemName = $request->get('item_name', '');

        $instruction = <<<TEXT
        The user is looking for "{$itemName}" in their home inventory. Please do the following:

        1. Read the `inventory://items` resource, passing `search={$itemName}` as a query parameter, to find matching items.
        2. If one or more items are found, report for each:
           - The item name and ID
           - The storage location (from `stock_location.name`)
           - The specific position within the location if available (e.g. "top shelf")
           - The current quantity and unit
        3. If no items are found, let the user know and suggest checking alternate spellings or browsing `inventory://items` without a filter.
        4. If multiple items match, list all of them so the user can identify the right one.
        TEXT;

        return Response::text(trim($instruction));
    }
}
