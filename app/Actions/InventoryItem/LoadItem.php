<?php

namespace App\Actions\InventoryItem;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadItem
{
    use AsAction;

    public function handle(User $user, InventoryItem $inventoryItem)
    {
        return $inventoryItem->load('stockLocation');
    }

    public function asController(Request $request, InventoryItem $inventoryItem)
    {
        return $this->handle($request->user(), $inventoryItem);
    }
}
