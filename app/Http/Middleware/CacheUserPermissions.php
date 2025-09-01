<?php
// app/Http/Middleware/CacheUserPermissions.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $cacheKey = 'user_permissions_' . auth()->id();
            
            if (!Cache::has($cacheKey)) {
                $permissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();
                Cache::put($cacheKey, $permissions, now()->addHours(1));
            }
        }

        return $next($request);
    }
}