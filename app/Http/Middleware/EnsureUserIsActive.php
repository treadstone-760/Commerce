<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // works with Bearer token

        if ($user && $user->status === 'inactive') {
            return response()->json([
                'status' => Response::HTTP_FORBIDDEN,
                'message' => 'User account is inactive',
            ], 403);
        }

        return $next($request);
    }
}
