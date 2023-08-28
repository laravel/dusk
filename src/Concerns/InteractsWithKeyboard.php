<?php

namespace Laravel\Dusk\Concerns;

use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Keyboard;

trait InteractsWithKeyboard
{
    /**
     * Uses browser's keyboard.
     *
     * @param  callable(\Laravel\Dusk\Keyboard):void  $callback
     * @return $this
     */
    protected function usesKeyboard(callable $callback)
    {
        if (is_callable($callback)) {
            call_user_func($callback, new Keyboard($this));
        }

        return $this;
    }

    /**
     * Parse the keys before sending to the keyboard.
     *
     * @param  array  $keys
     * @return array
     */
    protected function parseKeys($keys)
    {
        return collect($keys)->map(function ($key) {
            if (is_string($key) && Str::startsWith($key, '{') && Str::endsWith($key, '}')) {
                $key = constant(WebDriverKeys::class.'::'.strtoupper(trim($key, '{}')));
            }

            if (is_array($key) && Str::startsWith($key[0], '{')) {
                $key[0] = constant(WebDriverKeys::class.'::'.strtoupper(trim($key[0], '{}')));
            }

            return $key;
        })->all();
    }
}
