<?php

namespace Laravel\Dusk\Concerns;

use DateTimeInterface;
use Facebook\WebDriver\Exception\NoSuchCookieException;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Crypt;

trait InteractsWithCookies
{
    /**
     * Get or set an encrypted cookie's value.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  int|DateTimeInterface|null  $expiry
     * @param  array  $options
     * @return $this|string|null
     */
    public function cookie($name, $value = null, $expiry = null, array $options = [])
    {
        if (! is_null($value)) {
            return $this->addCookie($name, $value, $expiry, $options);
        }

        try {
            $cookie = $this->driver->manage()->getCookieNamed($name);
        } catch (NoSuchCookieException $e) {
            $cookie = null;
        }

        if ($cookie) {
            $decryptedValue = decrypt(rawurldecode($cookie['value']), $unserialize = false);

            $hasValuePrefix = strpos($decryptedValue, CookieValuePrefix::create($name, Crypt::getKey())) === 0;

            return $hasValuePrefix ? CookieValuePrefix::remove($decryptedValue) : $decryptedValue;
        }
    }

    /**
     * Get or set an unencrypted cookie's value.
     *
     * @param  string  $name
     * @param  string|null  $value
     * @param  int|DateTimeInterface|null  $expiry
     * @param  array  $options
     * @return $this|string|null
     */
    public function plainCookie($name, $value = null, $expiry = null, array $options = [])
    {
        if (! is_null($value)) {
            return $this->addCookie($name, $value, $expiry, $options, false);
        }

        try {
            $cookie = $this->driver->manage()->getCookieNamed($name);
        } catch (NoSuchCookieException $e) {
            $cookie = null;
        }

        if ($cookie) {
            return rawurldecode($cookie['value']);
        }
    }

    /**
     * Add the given cookie.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int|DateTimeInterface|null  $expiry
     * @param  array  $options
     * @param  bool  $encrypt
     * @return $this
     */
    public function addCookie($name, $value, $expiry = null, array $options = [], $encrypt = true)
    {
        if ($encrypt) {
            $prefix = CookieValuePrefix::create($name, Crypt::getKey());

            $value = encrypt($prefix.$value, $serialize = false);
        }

        if ($expiry instanceof DateTimeInterface) {
            $expiry = $expiry->getTimestamp();
        }

        $this->driver->manage()->addCookie(
            array_merge($options, compact('expiry', 'name', 'value'))
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
