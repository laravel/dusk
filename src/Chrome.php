<?php

namespace Laravel\Dusk;

use Symfony\Component\Process\Process;

trait Chrome
{
    /**
     * Prepare the test suite for execution.
     *
     * @beforeClass
     * @return void
     */
    public static function startChromeDriver()
    {
        if (PHP_OS === 'Darwin') {
            $process = new Process('./chromedriver-mac', realpath(__DIR__.'/../bin'), null, null, null);
        } else {
            $process = new Process('./chromedriver-linux', realpath(__DIR__.'/../bin'), null, null, null);
        }

        $process->start();
    }
}
