<?php

namespace Laravel\Dusk\Chrome;

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
     * @param  array  $arguments
     * @return void
     *
     * @throws \RuntimeException
     */
    public static function startChromeDriver(array $arguments = [])
    {
        static::$chromeProcess = static::buildChromeProcess($arguments);

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
     * @param  array  $arguments
     * @return \Symfony\Component\Process\Process
     *
     * @throws \RuntimeException
     */
    protected static function buildChromeProcess(array $arguments = [])
    {
        return (new ChromeProcess(static::$chromeDriver))->toProcess($arguments);
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
