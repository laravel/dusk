<?php

namespace Laravel\Dusk\Chrome;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait SupportsChrome
{
    /**
     * The path to the custom Chromedriver binary.
     *
     * @var string|null
     */
    protected static $chromeDriver;

    /**
     * The Chromedriver process instance.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected static $chromeProcess;

    /**
     * Start the Chromedriver process.
     *
     * @throws \RuntimeException if the driver file path doesn't exist.
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
     * @throws \RuntimeException if the driver file path doesn't exist.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected static function buildChromeProcess()
    {
        return (new ChromeProcess(static::$chromeDriver))->toProcess();
    }

    /**
     * Set the path to the custom Chromedriver.
     *
     * @param  string  $path
     * @return void
     */
    public static function useChromedriver($path)
    {
        static::$chromeDriver = $path;
    }
}
