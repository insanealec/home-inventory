<?php

namespace App\Actions\StockLocation;

use App\Models\StockLocation;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStockLocation
{
    use AsAction;

    public function handle(StockLocation $stockLocation, array $data)
    {
        return $stockLocation->update($data) ? $stockLocation : null;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:stock_locations,id',
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string',
        ];
    }

    public function asController(Request $request, StockLocation $stockLocation)
    {
        return $this->handle($stockLocation, $request->all());
    }
}
