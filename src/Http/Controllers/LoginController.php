<?php

namespace Laravel\Dusk\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Laravel\Dusk\Concerns\ManagesAuthenticationTokens;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    use ManagesAuthenticationTokens;
    /**
     * Login using the given user ID / email.
     *
     * @param  string  $userId
     * @return Response
     * @throws AuthenticationException
     */
    public function login($userId)
    {
        \Log::debug('here');
        if (!$this->verifyAuthToken($userId, request('token'))) {
            throw new AuthenticationException('Invalid Dusk Token for userId');
        }
        \Log::debug('somehow');

        $model = config('auth.providers.users.model');

        if (str_contains($userId, '@')) {
            $user = (new $model)->where('email', $userId)->first();
        } else {
            $user = (new $model)->find($userId);
        }

        Auth::login($user);
    }
}
