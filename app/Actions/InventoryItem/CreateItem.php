<?php

namespace App\Actions\InventoryItem;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateItem
{
    use AsAction;

    public function handle(User $user, array $data)
    {
        return $user->inventoryItems()->create($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'stock_location_id' => 'nullable|exists:stock_locations,id',
            'position' => 'nullable|string|max:255',
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->all());
    }
}
