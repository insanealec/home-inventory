<?php

namespace App\Actions\InventoryItem;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadItemsFilters
{
    public $search;

    public $stockLocationId;

    public $sortBy;

    public $sortDirection;

    public $page;

    public $perPage;
}

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
        $filters = new LoadItemsFilters;
        $filters->search = $request->input('search');
        $filters->stockLocationId = $request->input('stock_location_id');
        $filters->sortBy = $request->input('sort_by', 'created_at');
        $filters->sortDirection = $request->input('sort_direction', 'desc');
        $filters->page = $request->input('page', 1);
        $filters->perPage = $request->input('per_page', 15);

        return $this->handle($request->user(), $filters);
    }
}
