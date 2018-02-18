<?php

namespace Laravel\Dusk\Http\Controllers;

use Laravel\Dusk\Facades\Faking;

class FakingController
{
    /**
     * Setup a facade fake.
     *
     * @param  string   $facade
     * @return void
     */
    public function fake(string $facade)
    {
        Faking::fake($facade);
    }

    /**
     * Retrieve serialized facade fake.
     *
     * @param  string   $facade
     * @return mixed
     */
    public function getFake(string $facade)
    {
        if (Faking::has($facade)) {
            return response()->json(Faking::serialize($facade));
        }
    }
}
