<?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class WhatDoINeedToBuyPrompt extends Prompt
{
    protected string $name = 'what_do_i_need_to_buy';

    protected string $description = 'Analyse the inventory and produce a prioritised shopping list of items that need restocking.';

    public function arguments(): array
    {
        return [
            new Argument(
                name: 'create_list',
                description: 'Whether to automatically create a shopping list from the results. Pass "yes" to create one, omit or pass "no" to just report.',
                required: false,
            ),
        ];
    }

    public function handle(Request $request): Response
    {
        $createList = strtolower($request->get('create_list', 'no')) === 'yes';

        $instruction = <<<TEXT
        The user wants to know what they need to buy. Please do the following:

        1. Read the `inventory://dashboard` resource to get a high-level overview, paying attention to `low_stock_items` and `expiring_items`.
        2. Use the `find_low_stock_items` tool to get the full list of items below their reorder point (not just the dashboard preview of 5).
        3. Present the results clearly, grouped by urgency:
           - **Urgent** — items with zero quantity or expiring soon
           - **Low stock** — items below reorder point but not yet zero
        4. Include each item's name, current quantity, reorder point, and storage location if available.
        TEXT;

        if ($createList) {
            $instruction .= <<<TEXT


        5. After presenting the results, use `create_shopping_list` to create a new list called "Restock — {today's date}" and add all low-stock items to it using `add_item_to_shopping_list`, linking each to its inventory item via `inventory_item_id`. Report which items were added.
        TEXT;
        }

        return Response::text(trim($instruction));
    }
}
