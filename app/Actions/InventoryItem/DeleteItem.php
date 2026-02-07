<?php

namespace App\Actions\InventoryItem;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteItem
{
    use AsAction;

    public function handle(User $user, InventoryItem $inventoryItem): bool
    {
        return $inventoryItem->delete();
    }

    public function asController(Request $request, InventoryItem $inventoryItem)
    {
        $this->handle($request->user(), $inventoryItem);

        return response()->json(['message' => 'Inventory item deleted successfully.']);
    }
}
