<?php

namespace Laravel\Dusk\Concerns;

trait InteractsWithJavascript
{
    /**
     * Execute single or multiple Javascript code
     *
     * @param string|array $scripts
     * @return $this
     */
    public function executeScripts($scripts)
    {
        $this->ensurejQueryIsAvailable();

        if (is_array($scripts)) {
            foreach ($scripts as $script) {
                $this->driver->executeScript($script);
            }

            return $this;
        }

        $this->driver->executeScript($scripts);

        return $this;
    }
}
