<?php

namespace App\Actions\StockLocation;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateStockLocation
{
    use AsAction;

    public function handle(User $user, array $data)
    {
        return $user->stockLocations()->create($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string',
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->all());
    }
}
