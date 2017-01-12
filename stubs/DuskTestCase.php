<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Configure the Dusk browser driver.
     *
     * @beforeClass
     * @return void
     */
    public static function browser()
    {
        static::useChrome();
    }
}
