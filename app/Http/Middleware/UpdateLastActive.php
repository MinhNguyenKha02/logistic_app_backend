<?php

namespace App\Http\Middleware;

use Barryvdh\Debugbar\Twig\Extension\Debug;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateLastActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard("api")->check()) {
            $user = Auth::guard("api")->user();
            $user->update([
                'last_active_at' => now(),
                'is_active' => true,
            ]);
        }
        return $next($request);
    }
}
