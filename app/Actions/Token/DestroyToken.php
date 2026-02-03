<?php

namespace App\Actions\Token;

use App\Models\User;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyToken
{
    use AsAction;

    /**
     * Destroy the given token for the given user.
     */
    public function handle(?User $user, string $tokenId)
    {
        if (! $user) {
            return collect();
        }

        return $user->tokens->where('id', $tokenId)->first()?->delete() ?? false;
    }

    public function asController(Request $request, string $tokenId)
    {
        $user = $request->user();

        return $this->handle($user, $tokenId);
    }
}
