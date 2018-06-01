<?php

namespace Laravel\Dusk\Chrome;

use Symfony\Component\Process\Process;

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
     * @param array $args
     *
     * @throws \RuntimeException if the driver file path doesn't exist.
     *
     * @return void
     */
    public static function startChromeDriver(array $args = [])
    {
        static::$chromeProcess = static::buildChromeProcess($args);

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
     *
     * @return \Symfony\Component\Process\Process
     * @throws \RuntimeException if the driver file path doesn't exist.
     */
    protected static function buildChromeProcess(array $args = [])
    {
        return (new ChromeProcess(static::$chromeDriver))->toProcess($args);
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
