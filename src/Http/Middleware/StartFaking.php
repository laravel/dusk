<?php

namespace Laravel\Dusk\Http\Middleware;

use Closure;
use Laravel\Dusk\Facades\Faking;

class StartFaking
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
        Faking::start();

        return $next($request);
    }
}
