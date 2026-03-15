<?php

namespace App\Actions\ShoppingList;

use App\Models\ShoppingList;
use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateShoppingListAction
{
    use AsAction;

    public function handle(User $user, array $data): ShoppingList
    {
        return $user->shoppingLists()->create($data);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean',
            'shopping_date' => 'nullable|date',
        ];
    }

    public function asController(Request $request)
    {
        return $this->handle($request->user(), $request->all());
    }
}