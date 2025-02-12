<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponseTrait;

class EnsureIsGuest
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('user-api')->check() || Auth::guard('admin-api')->check()) {
            return $this->unauthorizedResponse();
        }

        return $next($request);
    }
}