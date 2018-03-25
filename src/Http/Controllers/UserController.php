<?php

namespace Laravel\Dusk\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class UserController
{
    /**
     * Retrieve the authenticated user identifier and class name.
     *
     * @param  string|null  $guard
     * @return array
     */
    public function user($guard = null)
    {
        $user = Auth::guard($guard)->user();

        if (! $user) {
            return [];
        }

        return [
            'id' => $user->getAuthIdentifier(),
            'className' => get_class($user),
        ];
    }

    /**
     * Login using the given user ID / email.
     *
     * @param  string  $userId
     * @param  string  $guard
     */
    public function login($userId, $guard = null)
    {
        $userProvider = Auth::getProvider();

        if (str_contains($userId, '@')) {
            $user = $userProvider->retrieveByCredentials(['email' => $userId]);
        } else {
            $user = $userProvider->retrieveById($userId);
        }

        $guard = $guard ?: config('auth.defaults.guard');
        Auth::guard($guard)->login($user);
    }

    /**
     * Log the user out of the application.
     *
     * @param  string  $guard
     */
    public function logout($guard = null)
    {
        Auth::guard($guard ?: config('auth.defaults.guard'))->logout();
    }

    /**
     * Get the model for the given guard.
     *
     * @param  string  $guard
     * @return string
     */
    protected function modelForGuard($guard)
    {
        $provider = config("auth.guards.{$guard}.provider");

        return config("auth.providers.{$provider}.model");
    }
}
