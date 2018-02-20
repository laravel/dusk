<?php

namespace Laravel\Dusk\Faking;

use Symfony\Component\HttpFoundation\Response;

abstract class Driver
{
    /**
     * The array of active facade fakes.
     *
     * @var array
     */
    protected $fakes = [];

    /**
     * Start faking facades.
     *
     * @return void
     */
    public function start()
    {
        $this->loadFakes();

        foreach ($this->fakes as $facade => $fake) {
            $facade::swap($fake);
        }
    }

    /**
     * Save facades state.
     *
     * @param  \Symfony\Component\HttpFoundation\Response   $response
     * @return void
     */
    public function save(Response $response)
    {
        $this->storeFakes($response);
    }

    /**
     * Replace facade instance with a fake.
     *
     * @param  string   $facade
     * @param  mixed[]  ...$arguments
     * @return void
     */
    public function fake(string $facade, ...$arguments)
    {
        if (!$this->has($facade)) {
            $fake = $this->createFake($facade, ...$arguments);
            $facade::swap($fake);
            $this->fakes[$facade] = $fake;
        }
    }

    /**
     * Determine if a facade is being faked.
     *
     * @param  string   $facade
     * @return boolean
     */
    public function has(string $facade)
    {
        return isset($this->fakes[$facade]);
    }

    /**
     * Serialize a facade fake.
     *
     * @param  string   $facade
     * @return string
     */
    public function serialize(string $facade)
    {
        return serialize($this->fakes[$facade]);
    }

    /**
     * Deserialize a facade fake.
     *
     * @param  string   $serializedFake
     * @return mixed
     */
    public function unserialize(string $serializedFake)
    {
        return unserialize($serializedFake);
    }

    /**
    * Create a facade fake.
    *
    * @param $facade   string
    * @param  mixed[]  ...$arguments
    * @return mixed
    */
    protected function createFake(string $facade, ...$arguments)
    {
        $facade::fake(...$arguments);

        return $facade::getFacadeRoot();
    }

    /**
     * Load fakes from storage.
     *
     * @return void
     */
    protected abstract function loadFakes();

    /**
     * Store fakes.
     *
     * @param  \Symfony\Component\HttpFoundation\Response   $response
     * @return void
     */
    protected abstract function storeFakes(Response $response);
}
