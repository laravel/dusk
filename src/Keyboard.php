<?php

namespace Laravel\Dusk;

use BadMethodCallException;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin \Facebook\WebDriver\Remote\RemoteKeyboard
 */
class Keyboard
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The browser instance.
     *
     * @var \Laravel\Dusk\Browser
     */
    public $browser;

    /**
     * Create a keyboard instance.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * Press the key using keyboard.
     */
    public function press($key)
    {
        $this->pressKey($key);

        return $this;
    }

    /**
     * Release the key using keyboard.
     */
    public function release($key)
    {
        $this->releaseKey($key);

        return $this;
    }

    /**
     * Type the keys using keyboard.
     *
     * @param  string|array<int, string>  $keys
     * @return $this
     */
    public function type($keys)
    {
        $this->sendKeys($keys);

        return $this;
    }

    /**
     * Dynamically call a method on the keyboard.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $mouseInteractions = [
            'mouseover',
            'click',
            'clickAtPoint',
            'clickAtXPath',
            'clickAndHold',
            'doubleClick',
            'rightClick',
            'releaseMouse',
        ];

        if (in_array($method, $mouseInteractions)) {
            $this->browser->{$method}(...$parameters);

            return $this;
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $keyboard = $this->browser->driver->getKeyboard();

        if (method_exists($keyboard, $method)) {
            $response = $keyboard->{$method}(...$parameters);

            if ($response === $keyboard) {
                return $this;
            } else {
                return $response;
            }
        }

        throw new BadMethodCallException("Call to undefined method [{$method}].");
    }
}
