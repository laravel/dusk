<?php

namespace Innobird\Dusky\Facades;

use Illuminate\Support\Facades\Facade;

class Dusky extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'dusky';
    }
}
