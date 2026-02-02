<?php

namespace App\Actions\Token;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateToken
{
    use AsAction;

    /**
     * Create a new token for the given user.
     */
    public function handle(?User $user, string $name, array $abilities = ['*'])
    {
        if (!$user) {
            return null;
        }
        $token = $user->createToken($name, $abilities);
        return $token;
    }

    public function asController(Request $request)
    {
        $user = $request->user();
        $name = $request->input('name');
        $abilities = $request->input('abilities', ['*']);
        return ($this->handle($user, $name, $abilities));
    }
}