<?php

namespace Laravel\Dusk\Chrome;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ChromeProcess
{
    /**
     * The path to the Chromedriver.
     *
     * @var String
     */
    protected $driver;

    public function __construct($driver = null)
    {
        $this->driver = $driver;

        if (!is_null($driver) && realpath($driver) === false) {
            throw new RuntimeException("Invalid path to Chromedriver [{$driver}].");
        }
    }

    public function build()
    {
        // First we check if a custom driver has been provided. For backward compatibility,
        // any custom driver is handled by Symfony Process Builder.
        if ($this->driver) {
            return $this->processBuilder();
        }

        // Process Builder is capable of building a process for Windows machine at the cost
        // of not being able to terminate it after.
        if ($this->isWindows()) {
            $this->driver = realpath(__DIR__.'/../../bin/chromedriver-win.exe');
            return $this->processBuilder();
        }

        // For Mac and Linux we can build a process without using Process Builder.
        // The process will automatically be killed once the execution of the script finishes.
        if ($this->isDarwin()) {
            $this->driver = realpath(__DIR__.'/../../bin/chromedriver-mac');
        } else {
            $this->driver = realpath(__DIR__.'/../../bin/chromedriver-linux');
        }

        return $this->process();
    }

    /**
     * Build Chrome Process.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function process()
    {
        return (new Process([realpath($this->driver)], null, $this->chromeEnvironment()));
    }

    /**
     * Build Chrome process through Symfony ProcessBuilder component.
     * The process cannot be automatically killed afterwards.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function processBuilder()
    {
        return (new ProcessBuilder)
            ->setPrefix(realpath($this->driver))
            ->getProcess()
            ->setEnv($this->chromeEnvironment());
    }

    /**
     * Get the Chromedriver environment variables.
     *
     * @return array
     */
    protected function chromeEnvironment()
    {
        if ($this->isDarwin() || $this->isWindows()) {
            return [];
        }

        return ['DISPLAY' => ':0'];
    }

    protected function isWindows()
    {
        return PHP_OS === 'WINNT';
    }

    protected function isDarwin()
    {
        return PHP_OS === 'Darwin';
    }
}