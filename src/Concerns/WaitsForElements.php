<?php

namespace Laravel\Dusk\Concerns;

use Carbon\Carbon;
use Closure;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\ScriptTimeoutException;
use Facebook\WebDriver\Exception\TimeoutException;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait WaitsForElements
{
    /**
     * Execute the given callback in a scoped browser once the selector is available.
     *
     * @param  string  $selector
     * @param  \Closure  $callback
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function whenAvailable($selector, Closure $callback, $seconds = null)
    {
        return $this->waitFor($selector, $seconds)->with($selector, $callback);
    }

    /**
     * Wait for the given selector to become visible.
     *
     * @param  string  $selector
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitFor($selector, $seconds = null)
    {
        $message = $this->formatTimeOutMessage('Waited %s seconds for selector', $selector);

        return $this->waitUsing($seconds, 100, function () use ($selector) {
            return $this->resolver->findOrFail($selector)->isDisplayed();
        }, $message);
    }

    /**
     * Wait for the given selector to be removed.
     *
     * @param  string  $selector
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitUntilMissing($selector, $seconds = null)
    {
        $message = $this->formatTimeOutMessage('Waited %s seconds for removal of selector', $selector);

        return $this->waitUsing($seconds, 100, function () use ($selector) {
            try {
                $missing = ! $this->resolver->findOrFail($selector)->isDisplayed();
            } catch (NoSuchElementException $e) {
                $missing = true;
            }

            return $missing;
        }, $message);
    }

    /**
     * Wait for the given text to be removed.
     *
     * @param  string  $text
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitUntilMissingText($text, $seconds = null)
    {
        $text = Arr::wrap($text);

        $message = $this->formatTimeOutMessage('Waited %s seconds for removal of text', implode("', '", $text));

        return $this->waitUsing($seconds, 100, function () use ($text) {
            return ! Str::contains($this->resolver->findOrFail('')->getText(), $text);
        }, $message);
    }

    /**
     * Wait for the given text to become visible.
     *
     * @param  array|string  $text
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForText($text, $seconds = null)
    {
        $text = Arr::wrap($text);

        $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

        return $this->waitUsing($seconds, 100, function () use ($text) {
            return Str::contains($this->resolver->findOrFail('')->getText(), $text);
        }, $message);
    }

    /**
     * Wait for the given text to become visible inside the given selector.
     *
     * @param  string  $selector
     * @param  array|string  $text
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForTextIn($selector, $text, $seconds = null)
    {
        $message = 'Waited %s seconds for text "'.$text.'" in selector '.$selector;

        return $this->waitUsing($seconds, 100, function () use ($selector, $text) {
            return $this->assertSeeIn($selector, $text);
        }, $message);
    }

    /**
     * Wait for the given link to become visible.
     *
     * @param  string  $link
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForLink($link, $seconds = null)
    {
        $message = $this->formatTimeOutMessage('Waited %s seconds for link', $link);

        return $this->waitUsing($seconds, 100, function () use ($link) {
            return $this->seeLink($link);
        }, $message);
    }

    /**
     * Wait for an input field to become visible.
     *
     * @param  string  $field
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitForInput($field, $seconds = null)
    {
        return $this->waitFor("input[name='{$field}'], textarea[name='{$field}'], select[name='{$field}']", $seconds);
    }

    /**
     * Wait for the given location.
     *
     * @param  string  $path
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForLocation($path, $seconds = null)
    {
        $message = $this->formatTimeOutMessage('Waited %s seconds for location', $path);

        return Str::startsWith($path, ['http://', 'https://'])
            ? $this->waitUntil('`${location.protocol}//${location.host}${location.pathname}` == \''.$path.'\'', $seconds, $message)
            : $this->waitUntil("window.location.pathname == '{$path}'", $seconds, $message);
    }

    /**
     * Wait for the given location using a named route.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForRoute($route, $parameters = [], $seconds = null)
    {
        return $this->waitForLocation(route($route, $parameters, false), $seconds);
    }

    /**
     * Wait until an element is enabled.
     *
     * @param  string  $selector
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitUntilEnabled($selector, $seconds = null)
    {
        $message = $this->formatTimeOutMessage('Waited %s seconds for element to be enabled', $selector);

        $this->waitUsing($seconds, 100, function () use ($selector) {
            return $this->resolver->findOrFail($selector)->isEnabled();
        }, $message);

        return $this;
    }

    /**
     * Wait until an element is disabled.
     *
     * @param  string  $selector
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitUntilDisabled($selector, $seconds = null)
    {
        $message = $this->formatTimeOutMessage('Waited %s seconds for element to be disabled', $selector);

        $this->waitUsing($seconds, 100, function () use ($selector) {
            return ! $this->resolver->findOrFail($selector)->isEnabled();
        }, $message);

        return $this;
    }

    /**
     * Wait until the given script returns true.
     *
     * @param  string  $script
     * @param  int|null  $seconds
     * @param  string|null  $message
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
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
     * Wait until the Vue component's attribute at the given key has the given value.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string|null  $componentSelector
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitUntilVue($key, $value, $componentSelector = null, $seconds = null)
    {
        $this->waitUsing($seconds, 100, function () use ($key, $value, $componentSelector) {
            return $value == $this->vueAttribute($componentSelector, $key);
        });

        return $this;
    }

    /**
     * Wait until the Vue component's attribute at the given key does not have the given value.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string|null  $componentSelector
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitUntilVueIsNot($key, $value, $componentSelector = null, $seconds = null)
    {
        $this->waitUsing($seconds, 100, function () use ($key, $value, $componentSelector) {
            return $value != $this->vueAttribute($componentSelector, $key);
        });

        return $this;
    }

    /**
     * Wait for a JavaScript dialog to open.
     *
     * @param  int|null  $seconds
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
     * @param  \Closure|null  $callback
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForReload($callback = null, $seconds = null)
    {
        $token = Str::random();

        $this->driver->executeScript("window['{$token}'] = {};");

        if ($callback) {
            $callback($this);
        }

        return $this->waitUsing($seconds, 100, function () use ($token) {
            return $this->driver->executeScript("return typeof window['{$token}'] === 'undefined';");
        }, 'Waited %s seconds for page reload.');
    }

    /**
     * Click an element and wait for the page to reload.
     *
     * @param  string|null  $selector
     * @param  int|null  $seconds
     * @return $this
     */
    public function clickAndWaitForReload($selector = null, $seconds = null)
    {
        return $this->waitForReload(function ($browser) use ($selector) {
            $browser->click($selector);
        }, $seconds);
    }

    /**
     * Wait for the given event type to occur on a target.
     *
     * @param  string  $type
     * @param  string|null  $target
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForEvent($type, $target = null, $seconds = null)
    {
        $seconds = is_null($seconds) ? static::$waitSeconds : $seconds;

        if ($target !== 'document' && $target !== 'window') {
            $target = $this->resolver->findOrFail($target ?? '');
        }

        $this->driver->manage()->timeouts()->setScriptTimeout($seconds);

        try {
            $this->driver->executeAsyncScript(
                'eval(arguments[0]).addEventListener(arguments[1], () => arguments[2](), { once: true });',
                [$target, $type]
            );
        } catch (ScriptTimeoutException $e) {
            throw new TimeoutException("Waited {$seconds} seconds for event [{$type}].");
        }

        return $this;
    }

    /**
     * Wait for the given callback to be true.
     *
     * @param  int|null  $seconds
     * @param  int  $interval
     * @param  \Closure  $callback
     * @param  string|null  $message
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
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
                throw new TimeoutException($message
                    ? sprintf($message, $seconds)
                    : "Waited {$seconds} seconds for callback."
                );
            }

            $this->pause($interval);
        }

        return $this;
    }

    /**
     * Prepare custom TimeoutException message for sprintf().
     *
     * @param  string  $message
     * @param  string  $expected
     * @return string
     */
    protected function formatTimeOutMessage($message, $expected)
    {
        return $message.' ['.str_replace('%', '%%', $expected).'].';
    }
}
