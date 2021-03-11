<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRoles
{
    /**
     * Handle the incoming request.
     * DEFINED in Kernel
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if($request->user()->hasAnyRoles([$role])){
           return  $next($request);
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}