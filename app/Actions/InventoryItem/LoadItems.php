<?php

namespace App\Actions\InventoryItem;

use App\Data\InventoryItem\LoadItemsFilters;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadItems
{
    use AsAction;

    public function handle(User $user, LoadItemsFilters $filters)
    {
        $query = $user->inventoryItems()->with('stockLocation');

        if ($filters->search) {
            $search = $filters->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($filters->stockLocationId) {
            $query->where('stock_location_id', $filters->stockLocationId);
        }

        $query->orderBy($filters->sortBy, $filters->sortDirection);

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), LoadItemsFilters::fromRequest($request));
    }
}
