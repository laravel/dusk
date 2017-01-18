<?php

namespace Laravel\Dusk;

use Symfony\Component\Process\Process;

trait SupportsChrome
{
    /**
     * The Chrome driver process instance.
     */
    protected static $chromeProcess;

    /**
     * Start the Chrome driver process.
     *
     * @return void
     */
    public static function startChromeDriver()
    {
        if (PHP_OS === 'Darwin') {
            static::$chromeProcess = new Process('./chromedriver-mac', realpath(__DIR__.'/../bin'), null, null, null);
        } else {
            static::$chromeProcess = new Process('./chromedriver-linux', realpath(__DIR__.'/../bin'), ['DISPLAY' => ':0'], null, null);
        }

        static::$chromeProcess->start();

        static::afterClass(function () {
            static::stopChromeDriver();
        });
    }

    /**
     * Stop the Chrome driver process.
     *
     * @return void
     */
    public static function stopChromeDriver()
    {
        if (static::$chromeProcess) {
            static::$chromeProcess->stop();
        }
    }
}
