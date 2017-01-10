<?php

namespace Laravel\Dusk\Concerns;

use DateTimeInterface;

trait InteractsWithCookies
{
    /**
     * Get or set an encrypted cookie's value.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  mixed  $expiration
     * @param  array  $options
     * @return string
     */
    public function cookie($name, $value = null, $expiration = null, array $options = [])
    {
        if ($value) {
            return $this->addCookie($name, $value, $expiration, $options);
        }

        if ($cookie = $this->driver->manage()->getCookieNamed($name)) {
            return decrypt(rawurldecode($cookie['value']));
        }
    }

    /**
     * Get or set a plain cookie's value.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  array  $options
     * @return string
     */
    public function plainCookie($name, $value = null, $expiration = null, array $options = [])
    {
        if ($value) {
            return $this->addCookie($name, $value, $expiration, $options, false);
        }

        if ($cookie = $this->driver->manage()->getCookieNamed($name)) {
            return rawurldecode($cookie['value']);
        }
    }

    /**
     * Add the given cookie.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  mixed  $expiration
     * @param  array  $options
     * @param  bool  $encrypt
     * @return $this
     */
    public function addCookie($name, $value, $expiration, array $options = [], $encrypt = true)
    {
        if ($encrypt) {
            $value = encrypt($value);
        }

        if ($expiration instanceof DateTimeInterface) {
            $expiration = $expiration->getTimestamp();
        }

        $this->driver->manage()->addCookie(
            array_merge($options, compact('name', 'value'))
        );

        return $this;
    }

    /**
     * Delete the given cookie.
     *
     * @param  string  $name
     * @return $this
     */
    public function deleteCookie($name)
    {
        $this->driver->manage()->deleteCookieNamed($name);

        return $this;
    }
}
