<?php

namespace Laravel\Dusk\Concerns;

use PHPUnit\Framework\Assert as PHPUnit;

trait MakesUrlAssertions
{
    /**
     * Assert that the current URL matches the given URL.
     *
     * @param  string  $url
     * @return $this
     */
    public function assertUrlIs($url)
    {
        $pattern = str_replace('\*', '.*', preg_quote($url, '/'));

        $segments = parse_url($this->driver->getCurrentURL());

        $currentUrl = sprintf(
            '%s://%s%s%s',
            $segments['scheme'],
            $segments['host'],
            array_get($segments, 'port', '') ? ':'.$segments['port'] : '',
            array_get($segments, 'path', '')
        );

        PHPUnit::assertRegExp(
            '/^'.$pattern.'$/u', $currentUrl,
            "Actual URL [{$this->driver->getCurrentURL()}] does not equal expected URL [{$url}]."
        );

        return $this;
    }

    /**
     * Assert that the current scheme matches the given scheme.
     *
     * @param  string  $scheme
     * @return $this
     */
    public function assertSchemeIs($scheme)
    {
        $pattern = str_replace('\*', '.*', preg_quote($scheme, '/'));

        $actual = parse_url($this->driver->getCurrentURL(), PHP_URL_SCHEME) ?? '';

        PHPUnit::assertRegExp(
            '/^'.$pattern.'$/u', $actual,
            "Actual scheme [{$actual}] does not equal expected scheme [{$pattern}]."
        );

        return $this;
    }

    /**
     * Assert that the current scheme does not match the given scheme.
     *
     * @param  string  $scheme
     * @return $this
     */
    public function assertSchemeIsNot($scheme)
    {
        $actual = parse_url($this->driver->getCurrentURL(), PHP_URL_SCHEME) ?? '';

        PHPUnit::assertNotEquals(
            $scheme, $actual,
            "Scheme [{$scheme}] should not equal the actual value."
        );

        return $this;
    }

    /**
     * Assert that the current host matches the given host.
     *
     * @param  string  $host
     * @return $this
     */
    public function assertHostIs($host)
    {
        $pattern = str_replace('\*', '.*', preg_quote($host, '/'));

        $actual = parse_url($this->driver->getCurrentURL(), PHP_URL_HOST) ?? '';

        PHPUnit::assertRegExp(
            '/^'.$pattern.'$/u', $actual,
            "Actual host [{$actual}] does not equal expected host [{$pattern}]."
        );

        return $this;
    }

    /**
     * Assert that the current host does not match the given host.
     *
     * @param  string  $host
     * @return $this
     */
    public function assertHostIsNot($host)
    {
        $actual = parse_url($this->driver->getCurrentURL(), PHP_URL_HOST) ?? '';

        PHPUnit::assertNotEquals(
            $host, $actual,
            "Host [{$host}] should not equal the actual value."
        );

        return $this;
    }

    /**
     * Assert that the current port matches the given port.
     *
     * @param  string  $port
     * @return $this
     */
    public function assertPortIs($port)
    {
        $pattern = str_replace('\*', '.*', preg_quote($port, '/'));

        $actual = parse_url($this->driver->getCurrentURL(), PHP_URL_PORT) ?? '';

        PHPUnit::assertRegExp(
            '/^'.$pattern.'$/u', $actual,
            "Actual port [{$actual}] does not equal expected port [{$pattern}]."
        );

        return $this;
    }

    /**
     * Assert that the current host does not match the given host.
     *
     * @param  string  $port
     * @return $this
     */
    public function assertPortIsNot($port)
    {
        $actual = parse_url($this->driver->getCurrentURL(), PHP_URL_PORT) ?? '';

        PHPUnit::assertNotEquals(
            $port, $actual,
            "Port [{$port}] should not equal the actual value."
        );

        return $this;
    }

    /**
     * Assert that the current URL path matches the given pattern.
     *
     * @param  string  $path
     * @return $this
     */
    public function assertPathIs($path)
    {
        $pattern = str_replace('\*', '.*', preg_quote($path, '/'));

        $actualPath = parse_url($this->driver->getCurrentURL(), PHP_URL_PATH) ?? '';

        PHPUnit::assertRegExp(
            '/^'.$pattern.'$/u', $actualPath,
            "Actual path [{$actualPath}] does not equal expected path [{$path}]."
        );

        return $this;
    }

    /**
     * Assert that the current URL path begins with given path.
     *
     * @param  string  $path
     * @return $this
     */
    public function assertPathBeginsWith($path)
    {
        $actualPath = parse_url($this->driver->getCurrentURL(), PHP_URL_PATH) ?? '';

        PHPUnit::assertStringStartsWith(
            $path, $actualPath,
            "Actual path [{$actualPath}] does not begin with expected path [{$path}]."
        );

        return $this;
    }

    /**
     * Assert that the current URL path does not match the given path.
     *
     * @param  string  $path
     * @return $this
     */
    public function assertPathIsNot($path)
    {
        $actualPath = parse_url($this->driver->getCurrentURL(), PHP_URL_PATH) ?? '';

        PHPUnit::assertNotEquals(
            $path, $actualPath,
            "Path [{$path}] should not equal the actual value."
        );

        return $this;
    }

    /**
     * Assert that the current URL fragment matches the given pattern.
     *
     * @param  string  $fragment
     * @return $this
     */
    public function assertFragmentIs($fragment)
    {
        $pattern = preg_quote($fragment, '/');

        $actualFragment = (string) parse_url($this->driver->executeScript('return window.location.href;'), PHP_URL_FRAGMENT);

        PHPUnit::assertRegExp(
            '/^'.str_replace('\*', '.*', $pattern).'$/u', $actualFragment,
            "Actual fragment [{$actualFragment}] does not equal expected fragment [{$fragment}]."
        );

        return $this;
    }

    /**
     * Assert that the current URL fragment begins with given fragment.
     *
     * @param  string  $fragment
     * @return $this
     */
    public function assertFragmentBeginsWith($fragment)
    {
        $actualFragment = (string) parse_url($this->driver->executeScript('return window.location.href;'), PHP_URL_FRAGMENT);

        PHPUnit::assertStringStartsWith(
            $fragment, $actualFragment,
            "Actual fragment [$actualFragment] does not begin with expected fragment [$fragment]."
        );

        return $this;
    }

    /**
     * Assert that the current URL fragment does not match the given fragment.
     *
     * @param  string  $fragment
     * @return $this
     */
    public function assertFragmentIsNot($fragment)
    {
        $actualFragment = (string) parse_url($this->driver->executeScript('return window.location.href;'), PHP_URL_FRAGMENT);

        PHPUnit::assertNotEquals(
            $fragment, $actualFragment,
            "Fragment [{$fragment}] should not equal the actual value."
        );

        return $this;
    }

    /**
     * Assert that the current URL path matches the given route.
     *
     * @param  string  $route
     * @param  array  $parameters
     * @return $this
     */
    public function assertRouteIs($route, $parameters = [])
    {
        return $this->assertPathIs(route($route, $parameters, false));
    }

    /**
     * Assert that a query string parameter is present and has a given value.
     *
     * @param  string  $name
     * @param  string  $value
     * @return $this
     */
    public function assertQueryStringHas($name, $value = null)
    {
        $output = $this->assertHasQueryStringParameter($name);

        if (is_null($value)) {
            return $this;
        }

        PHPUnit::assertEquals(
            $value, $output[$name],
            "Query string parameter [{$name}] had value [{$output[$name]}], but expected [{$value}]."
        );

        return $this;
    }

    /**
     * Assert that the given query string parameter is missing.
     *
     * @param  string  $name
     * @return $this
     */
    public function assertQueryStringMissing($name)
    {
        $parsedUrl = parse_url($this->driver->getCurrentURL());

        if (! array_key_exists('query', $parsedUrl)) {
            PHPUnit::assertTrue(true);
            return $this;
        }

        parse_str($parsedUrl['query'], $output);

        PHPUnit::assertArrayNotHasKey(
            $name, $output,
            "Found unexpected query string parameter [{$name}] in [".$this->driver->getCurrentURL()."]."
        );

        return $this;
    }

    /**
     * Assert that the given query string parameter is present.
     *
     * @param  string  $name
     * @return array
     */
    protected function assertHasQueryStringParameter($name)
    {
        $parsedUrl = parse_url($this->driver->getCurrentURL());

        PHPUnit::assertArrayHasKey(
            'query', $parsedUrl,
            "Did not see expected query string in [".$this->driver->getCurrentURL()."]."
        );

        parse_str($parsedUrl['query'], $output);

        PHPUnit::assertArrayHasKey(
            $name, $output,
            "Did not see expected query string parameter [{$name}] in [".$this->driver->getCurrentURL()."]."
        );

        return $output;
    }
}
