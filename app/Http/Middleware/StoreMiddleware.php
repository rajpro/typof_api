<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use Closure;

class StoreMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $request->store = Store::find($user->store_id);
        return $next($request);
    }
}