<?php

namespace App\Actions\StockLocation;

use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteStockLocation
{
    use AsAction;

    public function handle(User $user, StockLocation $stockLocation): bool
    {
        return $stockLocation->delete();
    }

    public function asController(Request $request, StockLocation $stockLocation)
    {
        $this->handle($request->user(), $stockLocation);

        return response()->json(['message' => 'Stock location deleted successfully.']);
    }
}
