<?php

namespace Laravel\Dusk;

use InvalidArgumentException;

class Dusk
{
    /**
     * Register the Dusk service provider.
     *
     * @param  array  $options
     * @return void
     */
    public static function register(array $options = [])
    {
        if (static::duskEnvironment($options)) {
            app()->register(DuskServiceProvider::class);
        }
    }

    /**
     * Determine if Dusk may run in this environment.
     *
     * @param  array  $options
     * @return bool
     */
    protected static function duskEnvironment($options)
    {
        if (! isset($options['environments'])) {
            return false;
        }

        if (is_string($options['environments'])) {
            $options['environments'] = [$options['environments']];
        }

        if (! is_array($options['environments'])) {
            throw new InvalidArgumentException("Dusk environments must be listed as an array.");
        }

        return app()->environment(...$options['environments']);
    }
}
