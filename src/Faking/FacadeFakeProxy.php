<?php

namespace Laravel\Dusk\Faking;

use Exception;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Facades\Faking;

class FacadeFakeProxy
{
    /**
     * Browser where facade is being faked.
     *
     * @var Browser
     */
    private $browser;

    /**
     * The facade being faked.
     *
     * @var string
     */
    private $facade;

    /**
     * Create a new facade fake proxy client instance.
     *
     * @param  Browser  $browser
     * @param  string   $facade
     * @return void
     */
    public function __construct(Browser $browser, string $facade)
    {
        $this->browser = $browser;
        $this->facade = $facade;
    }

    /**
     * Dynamically pass method calls to the browser faking the facade.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (!is_null($fake = $this->getFacadeFake())) {
            return $fake->{$method}(...$parameters);
        } else {
            throw new Exception(
                'Unable to retrieve fake for ['.$this->facade.'].'
            );
        }
    }

    /**
     * Get fake instance of the facade.
     *
     * @return mixed
     */
    protected function getFacadeFake()
    {
        $response = $this->browser->visit(
            '/_dusk/get-fake/' . urlencode($this->facade)
        );

        $serializedFake = json_decode(
            strip_tags($response->driver->getPageSource())
        );

        return is_null($serializedFake)
            ? null
            : Faking::unserialize($serializedFake);
    }
}
