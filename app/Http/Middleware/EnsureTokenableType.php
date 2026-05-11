<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage in routes:  ->middleware('tokenable:user')
 *                   ->middleware('tokenable:company')
 *
 * Sanctum tokens are polymorphic (tokenable_type + tokenable_id).
 * This middleware ensures the authenticated model matches the expected type,
 * so company tokens can't access user-only routes and vice versa.
 */
class EnsureTokenableType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $modelClass = match ($type) {
            'user'    => \App\Models\User::class,
            'company' => \App\Models\Company::class,
            default   => null,
        };

        if ($modelClass && !($user instanceof $modelClass)) {
            return response()->json(['message' => 'Forbidden. Wrong account type.'], 403);
        }

        return $next($request);
    }
}
