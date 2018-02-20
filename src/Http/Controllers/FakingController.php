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
        $arguments = json_decode(request('arguments'));
        Faking::fake($facade, ...$arguments);
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
