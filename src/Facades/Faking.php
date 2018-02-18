<?php

namespace Laravel\Dusk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Laravel\Dusk\Faking\FakingManager
 * @see \Laravel\Dusk\Faking\Driver
 */
class Faking extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'faking';
    }
}
