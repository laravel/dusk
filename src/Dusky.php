<?php

namespace Innobird\Dusky;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use McCaulay\Duskless\Chrome\SupportsChrome;

class Duskless
{
    use Concerns\ProvidesBrowser,
        SupportsChrome;

    /**
     * @param \Illuminate\Support\Collection $arguments A list of remote web driver arguments.
     */
    private $arguments;

    /**
     * @param int $requestTimeout Set the maximum time of a request to remote Selenium WebDriver server
     */
    private $requestTimeout;

    /**
     * @param int $connectTimeout Set timeout for the connect phase to remote Selenium WebDriver server in ms.
     */
    private $connectTimeout;

    /**
     * Initialises the dusk browser and starts the chrome driver.
     *
     * @return void
     */
    public function __construct()
    {
        $this->arguments = collect();
        $this->setRequestTimeout(30000);
        $this->setConnectTimeout(30000);
    }

    /**
     * Start the browser.
     *
     * @return $this
     */
    public function start()
    {
        static::startChromeDriver();
        return $this;
    }

    /**
     * Stop the browser.
     *
     * @return $this
     */
    public function stop()
    {
        try {
            $this->closeAll();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            static::stopChromeDriver();
            return $this;
        }
    }

    /**
     * Set the request timeout.
     *
     * @return $this
     */
    public function setRequestTimeout($timeout)
    {
        $this->requestTimeout = $timeout;
        return $this;
    }

    /**
     * Set the connect timeout.
     *
     * @return $this
     */
    public function setConnectTimeout($timeout)
    {
        $this->connectTimeout = $timeout;
        return $this;
    }

    /**
     * Run the browser in headless mode.
     *
     * @return $this
     */
    public function headless()
    {
        return $this->addArgument('--headless');
    }

    /**
     * Disable the browser using gpu.
     *
     * @return $this
     */
    public function disableGpu()
    {
        return $this->addArgument('--disable-gpu');
    }

    /**
     * Disable the sandbox.
     *
     * @return $this
     */
    public function noSandbox()
    {
        return $this->addArgument('--no-sandbox');
    }

    /**
     * Set the initial browser window size.
     *
     * @param $width The browser width in pixels.
     * @param $height The browser height in pixels.
     * @return $this
     */
    public function windowSize(int $width, int $height)
    {
        return $this->addArgument('--window-size=' . $width . ',' . $height);
    }

    /**
     * Set the user agent.
     *
     * @param $useragent The user agent to use.
     * @return $this
     */
    public function userAgent(string $useragent)
    {
        return $this->addArgument('--user-agent=' . $useragent);
    }

    /**
     * Add a browser option.
     *
     * @return $this
     */
    private function addArgument($argument)
    {
        if ($this->arguments->contains($argument)) {
            return;
        }
        $this->arguments->push($argument);
        return $this;
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments($this->arguments->toArray());

        return RemoteWebDriver::create(
            config('app.dusky_url', 'http://localhost:9515'),
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            ),
            $this->connectTimeout,
            $this->requestTimeout
        );
    }
}
