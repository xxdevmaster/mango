<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $access)
    {
        //echo ($access);
        dd($access);
        return $next($request);
    }
}
