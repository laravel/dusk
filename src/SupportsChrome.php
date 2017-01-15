<?php

namespace Laravel\Dusk;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

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
        static::initChromeDriver();

        static::$chromeProcess->start();

        static::afterClass(
            function () {
                static::stopChromeDriver();
            }
        );
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

    protected static function initChromeDriver()
    {
        static::$chromeProcess = (new ProcessBuilder())
            ->setPrefix(realpath(__DIR__.'/../bin/chromedriver-'.static::getOSSuffix().'.exe'))
            ->getProcess()
            ->setEnv(static::getOSEnv());
    }

    protected static function getOSEnv()
    {
        if (PHP_OS === 'Darwin' || PHP_OS === 'WINNT') {
            return [];
        }

        return ['DISPLAY' => ':0'];
    }

    protected static function getOSSuffix()
    {
        if (PHP_OS === 'Darwin') {
            return 'mac';
        }

        if (PHP_OS === 'WINNT') {
            return 'win.exe';
        }

        return 'linux';
    }
}
