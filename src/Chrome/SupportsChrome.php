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
     * @param  int  $port
     * @throws \RuntimeException if the driver file path doesn't exist.
     *
     * @return void
     */
    public static function startChromeDriver($port = null)
    {
        static::$chromeProcess = static::buildChromeProcess($port);

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
     * @param  int     $port
     *
     * @throws \RuntimeException if the driver file path doesn't exist.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected static function buildChromeProcess(int $port = null)
    {
        return (new ChromeProcess(static::$chromeDriver, $port))->toProcess();
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
