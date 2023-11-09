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
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next, ...$roleIds): Response
    {
        $hasPermission = false;
        $roleId = auth()->user()->role->id;
        foreach ($roleIds as $id) {
            if ($roleId === $id) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            throw new AuthorizationException();
        }

        return $next($request);
    }
}
