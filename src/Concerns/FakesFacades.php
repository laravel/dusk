<?php

namespace Laravel\Dusk\Concerns;

use Laravel\Dusk\Faking\FacadeFakeProxy;

trait FakesFacades
{
    /**
     * Fake a facade and return the fake proxy.
     *
     * @param  string   $facade
     * @param  mixed[]  ...$arguments
     * @return \Laravel\Dusk\Faking\FacadeFakeProxy
     */
    public function fake(string $facade, ...$arguments)
    {
        $this->visit(
            '/_dusk/fake/'.urlencode($facade).
            '?arguments='.urlencode(json_encode($arguments))
        );

        return new FacadeFakeProxy($this, $facade);
    }
}
