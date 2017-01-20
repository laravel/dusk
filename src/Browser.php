<?php

namespace Laravel\Dusk;

use Closure;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Facebook\WebDriver\WebDriverDimension;

class Browser
{
    use Concerns\InteractsWithAuthentication,
        Concerns\InteractsWithCookies,
        Concerns\InteractsWithElements,
        Concerns\InteractsWithMouse,
        Concerns\MakesAssertions,
        Concerns\WaitsForElements,
        Macroable {
            __call as macroCall;
        }

    /**
     * The base URL for all URLs.
     *
     * @var string
     */
    public static $baseUrl;

    /**
     * The directory that will contain any screenshots.
     *
     * @var string
     */
    public static $storeScreenshotsAt;

    /**
     * Get the callback which resolves the default user to authenticate.
     *
     * @var \Closure
     */
    public static $userResolver;

    /**
     * The RemoteWebDriver instance.
     *
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    public $driver;

    /**
     * The element resolver instance.
     *
     * @var ElementResolver
     */
    public $resolver;

    /**
     * The page object currently being viewed.
     *
     * @var mixed
     */
    public $page;

    /**
     * Create a browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @param  ElementResolver  $resolver
     * @return void
     */
    public function __construct($driver, $resolver = null)
    {
        $this->driver = $driver;

        $this->resolver = $resolver ?: new ElementResolver($driver);
    }

    /**
     * Browse to the given URL.
     *
     * @param  string  $url
     * @return $this
     */
    public function visit($url)
    {
        // First, if the URL is an object it means we are actually dealing with a page
        // and we need to create this page then get the URL from the page object as
        // it contains the URL. Once that is done, we will be ready to format it.
        if (is_object($url)) {
            $page = $url;

            $url = $page->url();
        }

        // If the URL does not start with http or https, then we will prepend the base
        // URL onto the URL and navigate to the URL. This will actually navigate to
        // the URL in the browser. Then we will be ready to make assertions, etc.
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            $url = static::$baseUrl.'/'.ltrim($url, '/');
        }

        $this->driver->navigate()->to($url);

        // If the page variable was set, we will call the "on" method which will set a
        // page instance variable and call an assert method on the page so that the
        // page can have the chance to verify that we are within the right pages.
        if (isset($page)) {
            $this->on($page);
        }

        return $this;
    }

    /**
     * Set the current page object.
     *
     * @param  mixed  $page
     * @return $this
     */
    public function on($page)
    {
        $this->page = $page;

        $page->assert($this);

        // Here we will set the page elements on the resolver instance, which will allow
        // the developer to access short-cuts for CSS selectors on the page which can
        // allow for more expressive navigation and interaction with all the pages.
        $this->resolver->pageElements(array_merge(
            $page::siteElements(), $page->elements()
        ));

        return $this;
    }

    /**
     * Refresh the page.
     *
     * @return $this
     */
    public function refresh()
    {
        $this->driver->navigate()->refresh();

        return $this;
    }

    /**
     * Maximize the browser window.
     *
     * @return $this
     */
    public function maximize()
    {
        $this->driver->manage()->window()->maximize();

        return $this;
    }

    /**
     * Resize the browser window.
     *
     * @param  int  $width
     * @param  int  $height
     * @return $this
     */
    public function resize($width, $height)
    {
        $this->driver->manage()->window()->setSize(
            new WebDriverDimension($width, $height)
        );

        return $this;
    }

    /**
     * Take a screenshot and store it with the given name.
     *
     * @param  string  $name
     * @return $this
     */
    public function screenshot($name)
    {
        $this->driver->takeScreenshot(
            sprintf('%s/%s.png', rtrim(static::$storeScreenshotsAt, '/'), $name)
        );

        return $this;
    }

    /**
     * Execute a Closure with a scoped browser instance.
     *
     * @param  string  $selector
     * @param  \Closure  $callback
     * @return $this
     */
    public function with($selector, Closure $callback)
    {
        $browser = new static(
            $this->driver, new ElementResolver($this->driver, $this->resolver->format($selector))
        );

        if ($this->page) {
            $browser->on($this->page);
        }

        call_user_func($callback, $browser);

        return $this;
    }

    /**
     * Ensure that jQuery is available on the page.
     *
     * @return void
     */
    protected function ensurejQueryIsAvailable()
    {
        if ($this->driver->executeScript("return window.jQuery == null")) {
            $this->driver->executeScript(file_get_contents(__DIR__.'/../bin/jquery.js'));
        }
    }

    /**
     * Pause for the given amount of milliseconds.
     *
     * @param  int  $milliseconds
     * @return $this
     */
    public function pause($milliseconds)
    {
        usleep($milliseconds * 1000);

        return $this;
    }

    /**
     * Close the browser.
     *
     * @return void
     */
    public function quit()
    {
        $this->driver->quit();
    }

    /**
     * Dynamically call a method on the browser.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        if ($this->page && method_exists($this->page, $method)) {
            array_unshift($parameters, $this);

            $this->page->{$method}(...$parameters);

            return $this;
        }

        throw new BadMethodCallException("Call to undefined method [{$method}].");
    }
}
