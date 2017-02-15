<?php

namespace Laravel\Dusk\Concerns;

use Laravel\Dusk\Browser;

trait InteractsWithAuthentication
{
    /**
     * Indicates if the user was authenticated using the loginAs method.
     *
     * @var bool
     */
    protected $loggedIn = false;

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

        $this->loggedIn = true;

        return $this->visit('/_dusk/login/'.$userId);
    }

    /**
     * Log out of the application.
     *
     * @return $this
     */
    public function logout()
    {
        $this->loggedIn = false;
        
        return $this->visit(route('logout', [], false));
    }

    /**
     * Log out a user if it is logged in.
     *
     * @return $this
     */
    public function logoutIfLoggedIn()
    {
        if ($this->loggedIn) {
            return $this->logout();
        }

        return $this;
    }
}
