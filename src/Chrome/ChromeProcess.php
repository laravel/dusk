<?php

namespace Laravel\Dusk\Chrome;

use RuntimeException;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ChromeProcess
{
    /**
     * The path to the Chromedriver.
     *
     * @var String
     */
    protected $driver;

    /**
     * Create a new ChromeProcess instance.
     *
     * @param  string  $driver
     * @return void
     */
    public function __construct($driver = null)
    {
        $this->driver = $driver;

        if (! is_null($driver) && realpath($driver) === false) {
            throw new RuntimeException("Invalid path to Chromedriver [{$driver}].");
        }
    }

    /**
     * Build the process to run Chromedriver.
     *
     * @param  array  $arguments
     * @return \Symfony\Component\Process\Process
     */
    public function toProcess(array $arguments = [])
    {
        if ($this->driver) {
            return $this->process($arguments);
        }

        if ($this->onWindows()) {
            $this->driver = realpath(__DIR__.'/../../bin/chromedriver-win.exe');

            return $this->process($arguments);
        }

        $this->driver = $this->onMac()
                        ? realpath(__DIR__.'/../../bin/chromedriver-mac')
                        : realpath(__DIR__.'/../../bin/chromedriver-linux');

        return $this->process($arguments);
    }

    /**
     * Build the Chromedriver with Symfony Process.
     *
     * @param  array  $arguments
     * @return \Symfony\Component\Process\Process
     */
    protected function process(array $arguments = [])
    {
        return (new Process(
            array_merge([realpath($this->driver)], $arguments), null, $this->chromeEnvironment()
        ));
    }

    /**
     * Get the Chromedriver environment variables.
     *
     * @return array
     */
    protected function chromeEnvironment()
    {
        if ($this->onMac() || $this->onWindows()) {
            return [];
        }

        return ['DISPLAY' => ':0'];
    }

    /**
     * Determine if Dusk is running on Windows or Windows Subsystem for Linux.
     *
     * @return bool
     */
    protected function onWindows()
    {
        return PHP_OS === 'WINNT' || Str::contains(php_uname(), 'Microsoft');
    }

    /**
     * Determine if Dusk is running on Mac.
     *
     * @return bool
     */
    protected function onMac()
    {
        return PHP_OS === 'Darwin';
    }
}
