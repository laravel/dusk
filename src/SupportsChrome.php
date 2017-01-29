<?php

namespace Laravel\Dusk;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait SupportsChrome
{
    /**
     * The Chromedriver process instance.
     */
    protected static $chromeProcess;

    /**
     * Start the Chromedriver process.
     *
     * @return void
     */
    public static function startChromeDriver()
    {
        static::$chromeProcess = static::buildChromeProcess();

        static::$chromeProcess->start();

        static::afterClass(function () {
            static::stopChromeDriver();
        });
    }

    /**
     * Stop the Chromedriver process.
     *
     * @return void
     */
    public static function stopChromeDriver()
    {
        if (static::$chromeProcess) {
            static::$chromeProcess->stop();
        }
    }

    /**
     * Build the process to run the Chromedriver.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected static function buildChromeProcess()
    {
        return (new ProcessBuilder())
                ->setPrefix(realpath(__DIR__.'/../bin/chromedriver-'.static::driverSuffix()))
                ->getProcess()
                ->setEnv(static::chromeEnvironment());
    }

    /**
     * Get the Chromedriver environment variables.
     *
     * @return array
     */
    protected static function chromeEnvironment()
    {
        if (PHP_OS === 'Darwin' || PHP_OS === 'WINNT') {
            return [];
        }

        return ['DISPLAY' => ':0'];
    }

    /**
     * Get the suffix for the Chromedriver binary.
     *
     * @return string
     */
    protected static function driverSuffix()
    {
        switch (PHP_OS) {
            case 'Darwin':
                return 'mac';
            case 'WINNT':
                return 'win.exe';
            default:
                return 'linux';
        }
    }
}
