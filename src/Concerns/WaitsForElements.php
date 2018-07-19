<?php

namespace Laravel\Dusk\Concerns;

use Closure;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverExpectedCondition;

trait WaitsForElements
{
    /**
     * Execute the given callback in a scoped browser once the selector is available.
     *
     * @param  string  $selector
     * @param  Closure  $callback
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function whenAvailable($selector, Closure $callback, $seconds = null)
    {
        return $this->waitFor($selector, $seconds)->with($selector, $callback);
    }

    /**
     * Wait for the given selector to be visible.
     *
     * @param  string  $selector
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitFor($selector, $seconds = null)
    {
        return $this->waitUsing($seconds, 100, function () use ($selector) {
            return $this->resolver->findOrFail($selector)->isDisplayed();
        }, "Waited %s seconds for selector [{$selector}].");
    }

    /**
     * Wait for the given selector to be removed.
     *
     * @param  string  $selector
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitUntilMissing($selector, $seconds = null)
    {
        return $this->waitUsing($seconds, 100, function () use ($selector) {
            try {
                $missing = ! $this->resolver->findOrFail($selector)->isDisplayed();
            } catch (NoSuchElementException $e) {
                $missing = true;
            }

            return $missing;
        }, "Waited %s seconds for removal of selector [{$selector}].");
    }

    /**
     * Wait for the given text to be visible.
     *
     * @param  string  $text
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForText($text, $seconds = null)
    {
        return $this->waitUsing($seconds, 100, function () use ($text) {
            return Str::contains($this->resolver->findOrFail('')->getText(), $text);
        }, "Waited %s seconds for text [{$text}].");
    }

    /**
     * Wait for the given link to be visible.
     *
     * @param  string  $link
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForLink($link, $seconds = null)
    {
        return $this->waitUsing($seconds, 100, function () use ($link) {
            return $this->seeLink($link);
        }, "Waited %s seconds for link [{$link}].");
    }

    /**
     * Wait for the given location.
     *
     * @param  string  $path
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForLocation($path, $seconds = null)
    {
        return $this->waitUntil("window.location.pathname == '{$path}'", $seconds, "Waited %s seconds for location [{$path}].");
    }

    /**
     * Wait for the given location using a named route.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForRoute($route, $parameters = [], $seconds = null)
    {
        return $this->waitForLocation(route($route, $parameters, false), $seconds);
    }

    /**
     * Wait until the given script returns true.
     *
     * @param  string  $script
     * @param  int  $seconds
     * @param  string  $message
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitUntil($script, $seconds = null, $message = null)
    {
        if (! Str::startsWith($script, 'return ')) {
            $script = 'return '.$script;
        }

        if (! Str::endsWith($script, ';')) {
            $script = $script.';';
        }

        return $this->waitUsing($seconds, 100, function () use ($script) {
            return $this->driver->executeScript($script);
        }, $message);
    }

    /**
     * Wait for a JavaScript dialog to open.
     *
     * @param  int  $seconds
     * @return $this
     */
    public function waitForDialog($seconds = null)
    {
        $seconds = is_null($seconds) ? static::$waitSeconds : $seconds;

        $this->driver->wait($seconds, 100)->until(
            WebDriverExpectedCondition::alertIsPresent(), "Waited {$seconds} seconds for dialog."
        );

        return $this;
    }

    /**
     * Wait for the current page to reload.
     *
     * @param  Closure  $callback
     * @param  int  $seconds
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitForReload($callback = null, $seconds = null)
    {
        $token = str_random();

        $this->driver->executeScript("window['{$token}'] = {};");

        if ($callback) {
            $callback($this);
        }

        return $this->waitUsing($seconds, 100, function () use ($token) {
            return $this->driver->executeScript("return typeof window['{$token}'] === 'undefined';");
        }, 'Waited %s seconds for page reload.');
    }

    /**
     * Wait for the given callback to be true.
     *
     * @param  int  $seconds
     * @param  int  $interval
     * @param  Closure  $callback
     * @param  string|null  $message
     * @return $this
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     */
    public function waitUsing($seconds, $interval, Closure $callback, $message = null)
    {
        $seconds = is_null($seconds) ? static::$waitSeconds : $seconds;

        $this->pause($interval);

        $started = Carbon::now();

        while (true) {
            try {
                if ($callback()) {
                    break;
                }
            } catch (Exception $e) {
                //
            }

            if ($started->lt(Carbon::now()->subSeconds($seconds))) {
                throw new TimeOutException($message
                    ? sprintf($message, $seconds)
                    : "Waited {$seconds} seconds for callback."
                );
            }

            $this->pause($interval);
        }

        return $this;
    }
}
