<?php

namespace Laravel\Dusk\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AddUserInfo
{
    /**
     * Add the current user ID and class name to the response header.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $this->appendUserInfo($response);
        }

        return $response;
    }

    /**
     * Append the info of the authenticated user to the response body.
     *
     * @param  \Illuminate\Http\Response  $response
     *
     * @return void
     */
    protected function appendUserInfo($response)
    {
        $user = Auth::user();

        $content = get_class($user).':'.$user->getAuthIdentifier();

        $html = "<script id='dusk_user_info' data-content='{$content}'></script></body>";

        $response->setContent(
            str_replace_last('</body>', $html, $response->getContent())
        );
    }
}
