<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDashboardSummaryAction
{
    use AsAction;

    public function handle(User $user): array
    {
        // Total inventory item count
        $totalItems = $user->inventoryItems()->count();

        // Total stock location count
        $totalLocations = $user->stockLocations()->count();

        // Active (incomplete) shopping list count
        $activeShoppingLists = $user->shoppingLists()
            ->where('is_completed', false)
            ->count();

        // Items where quantity <= reorder_point (low stock items, up to 5)
        $lowStockItems = $user->inventoryItems()
            ->where('reorder_point', '>', 0)
            ->whereColumn('quantity', '<=', 'reorder_point')
            ->orderBy('quantity')
            ->limit(5)
            ->get(['id', 'name', 'quantity', 'reorder_point']);

        // Items where expiration_date is within 30 days (up to 5)
        $expiringItems = $user->inventoryItems()
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('expiration_date')
            ->limit(5)
            ->get(['id', 'name', 'expiration_date', 'quantity']);

        // 5 most recently updated inventory items
        $recentItems = $user->inventoryItems()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'name', 'quantity', 'updated_at']);

        return [
            'total_items' => $totalItems,
            'total_locations' => $totalLocations,
            'active_shopping_lists' => $activeShoppingLists,
            'low_stock_items' => $lowStockItems,
            'expiring_items' => $expiringItems,
            'recent_items' => $recentItems,
        ];
    }

    public function asController(Request $request): array
    {
        return $this->handle($request->user());
    }
}
