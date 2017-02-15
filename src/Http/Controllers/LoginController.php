<?php

namespace Laravel\Dusk\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LoginController
{
    /**
     * Login using the given user ID / email.
     *
     * @param  string  $userId
     * @return Response
     */
    public function login($userId)
    {
        $model = config('auth.providers.users.model');

        if (str_contains($userId, '@')) {
            $user = (new $model)->where('email', $userId)->first();
        } else {
            $user = (new $model)->find($userId);
        }

        Auth::login($user);
    }

    /**
     * Log the user out of the application.
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();
    }
}
