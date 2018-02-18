<?php

namespace Laravel\Dusk\Faking\Drivers;

use Cookie;
use Laravel\Dusk\Faking\Driver;
use Symfony\Component\HttpFoundation\Response;

class CookiesDriver extends Driver
{
    /**
     * Name of the cookie used to serialize fakes.
     *
     * @var string
     */
    const COOKIE_NAME = 'Dusk-Facade-Fakes';

    /**
     * Load fakes from storage.
     *
     * @return void
     */
    protected function loadFakes()
    {
        $serializedFakes = Cookie::get(self::COOKIE_NAME, '{}');
        $serializedFakes = json_decode($serializedFakes, true);

        foreach ($serializedFakes as $facade => $serializedFake) {
            $this->fakes[$facade] = $this->unserialize($serializedFake);
        }
    }

    /**
     * Store fakes.
     *
     * @param  \Symfony\Component\HttpFoundation\Response   $response
     * @return void
     */
    protected function storeFakes(Response $response)
    {
        $serializedFakes = [];
        foreach (array_keys($this->fakes) as $facade) {
            $serializedFakes[$facade] = $this->serialize($facade);
        }

        $response->headers->setCookie(
            Cookie::forever(
                static::COOKIE_NAME,
                json_encode($serializedFakes),
                '/'
            )
        );
    }
}
