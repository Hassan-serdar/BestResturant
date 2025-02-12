<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserIsUser
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::guard('user-api')->check()) {
                return $next($request);
            }

            return $this->unauthorizedResponse();
        } catch (\Exception $e) {
            Log::error('EnsureUserIsUser Middleware Error: ' . $e->getMessage());
            return $this->serverErrorResponse('Internal server error');
        }
    }
}