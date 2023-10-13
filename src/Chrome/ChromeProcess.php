<?php

namespace Laravel\Dusk\Chrome;

use Laravel\Dusk\OperatingSystem;
use RuntimeException;
use Symfony\Component\Process\Process;

class ChromeProcess
{
    /**
     * The path to the Chromedriver.
     *
     * @var string|null
     */
    protected $driver;

    /**
     * Create a new ChromeProcess instance.
     *
     * @param  string|null  $driver
     * @return void
     */
    public function __construct($driver = null)
    {
        $this->driver = $driver;
    }

    /**
     * Build the process to run Chromedriver.
     *
     * @param  array  $arguments
     * @return \Symfony\Component\Process\Process
     *
     * @throws \RuntimeException
     */
    public function toProcess(array $arguments = [])
    {
        if ($this->driver) {
            $driver = $this->driver;
        } else {
            $filenames = [
                'linux' => 'chromedriver-linux',
                'mac' => 'chromedriver-mac',
                'mac-intel' => 'chromedriver-mac-intel',
                'mac-arm' => 'chromedriver-mac-arm',
                'win' => 'chromedriver-win.exe',
            ];

            $driver = __DIR__.'/../../bin'.DIRECTORY_SEPARATOR.$filenames[$this->operatingSystemId()];
        }

        $this->driver = realpath($driver);

        if ($this->driver === false) {
            throw new RuntimeException(
                "Invalid path to Chromedriver [{$driver}]. Make sure to install the Chromedriver first by running the dusk:chrome-driver command."
            );
        }

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
        return new Process(
            array_merge([$this->driver], $arguments), null, $this->chromeEnvironment()
        );
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

        return ['DISPLAY' => $_ENV['DISPLAY'] ?? ':0'];
    }

    /**
     * Determine if Dusk is running on Windows or Windows Subsystem for Linux.
     *
     * @return bool
     */
    protected function onWindows()
    {
        return OperatingSystem::onWindows();
    }

    /**
     * Determine if Dusk is running on Mac.
     *
     * @return bool
     */
    protected function onMac()
    {
        return OperatingSystem::onMac();
    }

    /**
     * Determine OS ID.
     *
     * @return string
     */
    protected function operatingSystemId()
    {
        return OperatingSystem::id();
    }
}
