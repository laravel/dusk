<?php

namespace Laravel\Dusk\Concerns;

trait InteractsWithJavascript
{
    /**
     * Execute JavaScript within the browser.
     *
     * @param  string|array $scripts
     * @return array
     */
    public function script($scripts)
    {
        collect((array) $scripts)->map(function ($script) {
            return $this->driver->executeScript($script);
        })->all();

        return $this;
    }
}
