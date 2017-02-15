<?php

namespace Laravel\Dusk\Concerns;

use Laravel\Dusk\Browser;
use PHPUnit_Framework_Assert as PHPUnit;

trait InteractsWithAuthentication
{
    /**
     * Log into the application as the default user.
     *
     * @return $this
     */
    public function login()
    {
        return $this->loginAs(call_user_func(Browser::$userResolver));
    }

    /**
     * Log into the application using a given user ID or email.
     *
     * @param  object|string  $userId
     * @return $this
     */
    public function loginAs($userId)
    {
        $userId = method_exists($userId, 'getKey') ? $userId->getKey() : $userId;

        return $this->visit('/_dusk/login/'.$userId);
    }

    /**
     * Log out of the application.
     *
     * @return $this
     */
    public function logout()
    {
        return $this->visit('/_dusk/logout/');
    }

    /**
     * Return the ID and the class name of the authenticated user.
     *
     * @param  string|null  $guard
     * @return array
     */
    protected function currentUserInfo($guard = null)
    {
        $response = $this->visit("/_dusk/user/$guard");

        return json_decode(strip_tags($response->driver->getPageSource()), true);
    }

    /**
     * Assert that the user is authenticated.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function assertAuthentication($guard = null)
    {
        PHPUnit::assertNotEmpty($this->currentUserInfo($guard), 'The user is not authenticated');

        return $this;
    }

    /**
     * Assert that the user is not authenticated.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function assertGuest($guard = null)
    {
        PHPUnit::assertEmpty($this->currentUserInfo($guard), 'The user is authenticated');

        return $this;
    }

    /**
     * Assert that the user is authenticated as the given user.
     *
     * @param  $user
     * @param  string|null  $guard
     * @return $this
     */
    public function assertAuthenticatedAs($user, $guard = null)
    {
        $expected = [
            'id' => $user->getAuthIdentifier(),
            'className' => get_class($user),
        ];

        PHPUnit::assertSame(
            $expected, $this->currentUserInfo($guard),
            'The currently authenticated user is not who was expected'
        );

        return $this;
    }
}
