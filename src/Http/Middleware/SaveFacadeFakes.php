<?php

namespace Laravel\Dusk\Http\Middleware;

use Closure;
use Laravel\Dusk\Facades\Faking;

class SaveFacadeFakes
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
        $response = $next($request);

        Faking::save($response);

        return $response;
    }
}
