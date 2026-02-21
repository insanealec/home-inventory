<?php

namespace App\Actions\StockLocation;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadStockLocations
{
    use AsAction;

    public function handle(User $user, array $filters = [])
    {
        $query = $user->stockLocations();

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortBy = $filters['sortBy'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';
        $query->orderBy($sortBy, $sortDirection);

        $perPage = $filters['perPage'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->all());
    }
}
