<?php

namespace Laravel\Dusk;

use BadMethodCallException;
use Illuminate\Support\Traits\Macroable;

class Keyboard
{
    use Macroable {
        __call as macroCall;
    };

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
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $keyboard = $browser->driver->getKeyboard();

        if (method_exists($keyboard, $method)) {
            $keyboard->{$method}(...$parameters);
        }

        throw new BadMethodCallException("Call to undefined method [{$method}].");
    }
}
