<?php

namespace Laravel\Dusk\Concerns;

use Laravel\Dusk\Faking\FacadeFakeProxy;

trait FakesFacades
{
    /**
     * Fake a facade and return the fake proxy.
     *
     * @param  string   $facade
     * @return \Laravel\Dusk\Faking\FacadeFakeProxy
     */
    public function fake(string $facade)
    {
        $this->visit('/_dusk/fake/'.urlencode($facade));

        return new FacadeFakeProxy($this, $facade);
    }
}
