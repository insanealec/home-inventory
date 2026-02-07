<?php

namespace App\Data\InventoryItem;

use Illuminate\Http\Request;

class LoadItemsFilters
{
    public function __construct(
        public ?string $search = null,
        public ?int $stockLocationId = null,
        public string $sortBy = 'created_at',
        public string $sortDirection = 'desc',
        public int $page = 1,
        public int $perPage = 15,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->input('search'),
            stockLocationId: $request->input('stock_location_id'),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc'),
            page: $request->input('page', 1),
            perPage: $request->input('per_page', 15),
        );
    }
}
