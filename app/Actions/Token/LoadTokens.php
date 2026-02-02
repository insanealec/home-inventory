<?php

namespace App\Actions\Token;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class LoadTokens
{
    use AsAction;

    /**
     * Load all tokens for the given user.
     */
    public function handle(?User $user)
    {
        if (! $user) {
            return collect();
        }

        return $user->tokens;
    }

    public function asController(Request $request)
    {
        $user = $request->user();

        return $this->handle($user)->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'created_at' => Carbon::parse($token->created_at)->format('Y-m-d H:i:s'),
                'last_used_at' => $token->last_used_at ? Carbon::parse($token->last_used_at)->format('Y-m-d H:i:s') : null,
                'expires_at' => $token->expires_at ? Carbon::parse($token->expires_at)->format('Y-m-d H:i:s') : null,
            ];
        });
    }
}
