<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStudentRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user || ! $user->hasRole('student', 'sanctum')) {
            return response()->json(['message' => 'User does not have the right roles.'], 403);
        }

        return $next($request);
    }
}
