<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserOwnsResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        foreach ($request->route()->parameters() as $parameter) {
            if ($parameter instanceof Model && property_exists($parameter, 'user_id')) {
                if ($parameter->user_id !== $user->id) {
                    throw new AuthorizationException;
                }
            }
        }

        return $next($request);
    }
}
