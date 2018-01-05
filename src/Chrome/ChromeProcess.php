<?php

namespace Laravel\Dusk\Chrome;

use RuntimeException;
use Illuminate\Support\Str;
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
     * @return \Symfony\Component\Process\Process
     */
    public function toProcess()
    {
        if ($this->driver) {
            return $this->fromProcessBuilder();
        }

        if ($this->onWindows()) {
            $this->driver = realpath(__DIR__.'/../../bin/chromedriver-win.exe');

            return $this->fromProcessBuilder();
        }

        $this->driver = $this->onMac()
                        ? realpath(__DIR__.'/../../bin/chromedriver-mac')
                        : realpath(__DIR__.'/../../bin/chromedriver-linux');

        return $this->process();
    }

    /**
     * Build the Chromedriver with Symfony Process.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function process()
    {
        return (new Process(
            [realpath($this->driver)], null, $this->chromeEnvironment()
        ));
    }

    /**
     * Build the Chrome process through Symfony ProcessBuilder component.
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function fromProcessBuilder()
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
