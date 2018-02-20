<?php

namespace Laravel\Dusk\Faking;

use Illuminate\Support\Manager;
use Laravel\Dusk\Faking\Drivers\CookiesDriver;

class FakingManager extends Manager
{
    /**
     * Get the default faking driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $config = $this->app['config'];

        return $config->has('faking')
            ? $config['faking.driver']
            : 'cookies';
    }

    /**
     * Create an instance of the Cookies faking driver.
     *
     * @return \Laravel\Dusk\Faking\Driver
     */
    protected function createCookiesDriver()
    {
        return new CookiesDriver;
    }
}
