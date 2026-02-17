<?php

namespace App\Actions\InventoryItem;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateItem
{
    use AsAction;

    public function handle(InventoryItem $item, array $data)
    {
        return $item->update($data) ? $item : null;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:inventory_items,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'stock_location_id' => 'nullable|exists:stock_locations,id',
            'position' => 'nullable|string|max:255',
        ];
    }

    public function asController(Request $request, InventoryItem $inventoryItem)
    {
        return $this->handle($inventoryItem, $request->all());
    }
}
