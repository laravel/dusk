<?php

namespace Laravel\Dusk\Console\Concerns;

trait InteractsWithTestingFrameworks
{
    /**
     * Determine if Pest is being used by the application.
     *
     * @return bool
     */
    protected function usingPest()
    {
        return function_exists('\Pest\\version') && file_exists(base_path('tests').'/Pest.php');
    }
}
