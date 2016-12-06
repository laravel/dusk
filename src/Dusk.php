<?php

namespace Laravel\Dusk;

class Dusk
{
    /**
     * Register the Dusk service provider.
     *
     * @return void
     */
    public static function register()
    {
        app()->register(DuskServiceProvider::class);
    }
}
