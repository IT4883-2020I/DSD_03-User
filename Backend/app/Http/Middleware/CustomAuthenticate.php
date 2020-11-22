<?php

namespace App\Http\Middleware;

use Closure;

class CustomAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //If the status is not approved redirect to login 
        if (Auth::check() && Auth::user()->status != 'ACTIVE'){
            Auth::logout();
            return redirect('/login')->with('error_login', 'Permission deny!');
        }
        return $next($request);
    }
}
