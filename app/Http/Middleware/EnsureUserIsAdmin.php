<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::guard('admin-api')->check()) {
                return $next($request);
            }

            return $this->unauthorizedResponse();
        } catch (\Exception $e) {
            Log::error('EnsureUserIsAdmin Middleware Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Internal server error');
        }
    }
}