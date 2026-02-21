<?php

namespace App\Actions\StockLocation;

use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadStockLocation
{
    use AsAction;

    public function handle(User $user, StockLocation $stockLocation)
    {
        return $stockLocation->load('inventoryItems');
    }

    public function asController(Request $request, StockLocation $stockLocation)
    {
        return $this->handle($request->user(), $stockLocation);
    }
}
