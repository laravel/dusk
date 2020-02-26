<?php

namespace Innobird\Dusky\Concerns;

use Closure;
use Exception;
use Illuminate\Support\Collection;
use Innobird\Dusky\Browser;
use ReflectionFunction;
use Throwable;

trait ProvidesBrowser
{
    /**
     * All of the active browser instances.
     *
     * @var array
     */
    protected static $browsers = [];

    /**
     * Create a new browser instance.
     *
     * @param  \Closure  $callback
     * @return \Innobird\Dusky\Browser|void
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function browse(Closure $callback)
    {
        $browsers = $this->createBrowsersFor($callback);

        try {
            $callback(...$browsers->all());
        } catch (Exception $e) {
            $this->captureFailuresFor($browsers);

            throw $e;
        } catch (Throwable $e) {
            $this->captureFailuresFor($browsers);

            throw $e;
        } finally {
            $this->storeConsoleLogsFor($browsers);

            static::$browsers = $this->closeAllButPrimary($browsers);
        }
        return $this;
    }

    /**
     * Create the browser instances needed for the given callback.
     *
     * @param  \Closure  $callback
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function createBrowsersFor(Closure $callback)
    {
        if (count(static::$browsers) === 0) {
            static::$browsers = collect([$this->newBrowser($this->createWebDriver())]);
        }

        $additional = $this->browsersNeededFor($callback) - 1;

        for ($i = 0; $i < $additional; $i++) {
            static::$browsers->push($this->newBrowser($this->createWebDriver()));
        }

        return static::$browsers;
    }

    /**
     * Create a new Browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @return \Innobird\Dusky\Browser
     */
    protected function newBrowser($driver)
    {
        return new Browser($driver);
    }

    /**
     * Get the number of browsers needed for a given callback.
     *
     * @param  \Closure  $callback
     * @return int
     *
     * @throws \ReflectionException
     */
    protected function browsersNeededFor(Closure $callback)
    {
        return (new ReflectionFunction($callback))->getNumberOfParameters();
    }

    /**
     * Capture failure screenshots for each browser.
     *
     * @param  \Illuminate\Support\Collection  $browsers
     * @return void
     */
    protected function captureFailuresFor($browsers)
    {
        $browsers->each(function ($browser, $key) {
            if (property_exists($browser, 'fitOnFailure') && $browser->fitOnFailure) {
                $browser->fitContent();
            }

            $name = $this->getCallerName();

            $browser->screenshot('failure-' . $name . '-' . $key);
        });
    }

    /**
     * Store the console output for the given browsers.
     *
     * @param  \Illuminate\Support\Collection  $browsers
     * @return void
     */
    protected function storeConsoleLogsFor($browsers)
    {
        $browsers->each(function ($browser, $key) {
            $name = $this->getCallerName();

            $browser->storeConsoleLog($name . '-' . $key);
        });
    }

    /**
     * Close all of the browsers except the primary (first) one.
     *
     * @param  \Illuminate\Support\Collection  $browsers
     * @return \Illuminate\Support\Collection
     */
    protected function closeAllButPrimary($browsers)
    {
        $browsers->slice(1)->each->quit();

        return $browsers->take(1);
    }

    /**
     * Close all of the active browsers.
     *
     * @return void
     */
    public static function closeAll()
    {
        Collection::make(static::$browsers)->each->quit();

        static::$browsers = collect();
    }

    /**
     * Create the remote web driver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     *
     * @throws \Exception
     */
    protected function createWebDriver()
    {
        return retry(5, function () {
            return $this->driver();
        }, 50);
    }

    /**
     * Get the browser caller name.
     *
     * @return string
     */
    protected function getCallerName()
    {
        return str_replace('\\', '_', get_class($this));
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    abstract protected function driver();
}
