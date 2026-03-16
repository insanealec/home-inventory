<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class AddGroceriesPrompt extends Prompt
{
    protected string $name = 'add_groceries';

    protected string $description = 'Add a list of grocery items to an existing or new shopping list, matching them against existing inventory items where possible.';

    public function arguments(): array
    {
        return [
            new Argument(
                name: 'items',
                description: 'A comma-separated list of grocery items to add (e.g. "milk, eggs, bread, olive oil").',
                required: true,
            ),
            new Argument(
                name: 'shopping_list_id',
                description: 'ID of an existing shopping list to add items to. If omitted, a new list will be created.',
                required: false,
            ),
        ];
    }

    public function handle(Request $request): Response
    {
        $items = $request->get('items', '');
        $shoppingListId = $request->get('shopping_list_id');

        if ($shoppingListId) {
            $listStep = "2. Read `shopping://lists/{$shoppingListId}` to confirm the list exists and show the user what is already on it.";
            $useListStep = "Use shopping list ID {$shoppingListId} for all additions.";
        } else {
            $listStep = '2. Use `create_shopping_list` to create a new shopping list. Ask the user for a name if none was given, or default to "Groceries — {today\'s date}".';
            $useListStep = 'Use the ID returned by `create_shopping_list` for all additions.';
        }

        $instruction = <<<TEXT
        The user wants to add the following groceries to a shopping list: {$items}

        Please do the following:

        1. Read `inventory://items` with a search for each item name to check whether it already exists in the inventory. Do this in a batch by searching broadly where possible.
        {$listStep}
        3. {$useListStep}
        4. For each grocery item:
           - If a matching inventory item was found, use `add_item_to_shopping_list` with `inventory_item_id` set to link them.
           - If no inventory match was found, use `add_item_to_shopping_list` with just the `name` field for a standalone entry.
           - Default quantity to 1 unless the user specified otherwise.
        5. After adding all items, provide a summary showing:
           - Which items were linked to existing inventory items
           - Which items were added as standalone entries
           - The full shopping list ID for reference
        TEXT;

        return Response::text(trim($instruction));
    }
}
