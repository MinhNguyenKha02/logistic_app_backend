<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Log;
use MongoDB\Driver\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
//    protected function redirectTo($request)
//    {
//        if (! $request->expectsJson() ) {
//            return route('login');
//        }
//    }

    public function handle($request, Closure $next, ...$guards)
    {
        if(session()->has("token")){
            $request->headers->set('Authorization', 'Bearer '.session('token'));
        }
        $this->authenticate($request, $guards);
        return $next($request);
    }
}
