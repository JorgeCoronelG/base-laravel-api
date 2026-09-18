<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next, ...$roleIds): Response
    {
        // Los parámetros de middleware (permission:1,2) llegan como string, por eso se compara como string.
        $roleId = (string) auth()->user()->role->id;

        if (! in_array($roleId, array_map('strval', $roleIds), true)) {
            throw new AuthorizationException;
        }

        return $next($request);
    }
}
