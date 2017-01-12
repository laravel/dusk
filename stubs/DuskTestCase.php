<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare the Dusk browser.
     *
     * @beforeClass
     * @return void
     */
    public static function prepareBrowser()
    {
        static::useChrome();
    }
}
