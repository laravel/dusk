<?php

namespace Laravel\Dusk;

use Closure;
use Exception;
use Throwable;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Illuminate\Foundation\Testing\TestCase as FoundationTestCase;

abstract class TestCase extends FoundationTestCase
{
    /**
     * The browser window being used for tests.
     *
     * @var \Laravel\Dusk\Browser
     */
    protected static $browser = null;

    /**
     * Register the base URL with Dusk.
     *
     * @before
     */
    public function propagateScaffoldingToBrowser()
    {
        Browser::$baseUrl = $this->baseUrl();

        Browser::$userResolver = function () {
            return $this->user();
        };
    }

    /**
     * Create a new browser instance.
     *
     * @return \Laravel\Dusk\Browser|void
     */
    public function browser()
    {
        if (static::$browser) {
            return static::$browser;
        }

        return static::$browser = new Browser(
            $driver = $this->createWebDriver()
        );
    }

    /**
     * Open a browser and pass it to the given callback. Close it when finished.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function withBrowser(Closure $callback)
    {
        $browser = new Browser($this->createWebDriver());

        try {
            $callback($browser);
        } catch (Exception $e) {
            $browser->screenshot('failure-'.time());

            throw $e;
        } catch (Throwable $e) {
            $browser->screenshot('failure-'.time());

            throw $e;
        } finally {
            collect($this->otherBrowsers)->each->quit();

            $browser->quit();
        }
    }

    /**
     * Create the remote web driver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function createWebDriver()
    {
        return RemoteWebDriver::create('http://localhost:9515', DesiredCapabilities::chrome());
    }

    /**
     * Determine the application's base URL.
     *
     * @var string
     */
    protected function baseUrl()
    {
        return config('app.url');
    }

    /**
     * Get a callback that returns the default user to authenticate.
     *
     * @return \Closure
     */
    protected function user()
    {
        throw new Exception("User resolver has not been set.");
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        static::$browser->quit();

        parent::tearDownAfterClass();
    }
}
