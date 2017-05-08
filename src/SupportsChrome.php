<?php

namespace Laravel\Dusk;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait SupportsChrome
{
    /**
     * The driver file path.
     *
     * @var string
     */
    protected static $driverPath = __DIR__.'/../bin';

    /**
     * The driver file to use.
     *
     * @var null|string
     */
    protected static $driverFile;

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
        $driverFilePath = static::driverPath().'/'.static::driverFile();

        if (realpath($driverFilePath) === false) {
            throw new RuntimeException("The \"$driverFilePath\" file doesn't exist.");
        }

        return (new ProcessBuilder())
                ->setPrefix(realpath($driverFilePath))
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
     * Get the path to the driver file directory.
     *
     * @return string
     */
    public static function driverPath()
    {
        return static::$driverPath;
    }

    /**
     * Set the directory for the driver file.
     *
     * @param  string  $path
     * @return void
     */
    public static function useDriverPath($path)
    {
        static::$driverPath = $path;
    }

    /**
     * Get the driver file used.
     *
     * @return string
     */
    public static function driverFile()
    {
        if (is_null(static::$driverFile)) {
            return 'chromedriver-'.static::driverSuffix();
        } else {
            return static::$driverFile;
        }
    }

    /**
     * Set the driver file to be used.
     *
     * @param  string  $file
     * @return void
     */
    public static function useDriverFile($file)
    {
        static::$driverFile = $file;
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
