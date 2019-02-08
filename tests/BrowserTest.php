<?php

namespace Laravel\Dusk\Tests;

use stdClass;
use Mockery as m;
use Laravel\Dusk\Page;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\WebDriverBrowserType;

class BrowserTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_visit()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit('/login');
    }

    public function test_visit_with_page_object()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);

        $this->assertEquals(['@modal' => '#modal'], $browser->resolver->elements);
        $this->assertEquals($page, $browser->page);
        $this->assertTrue($page->asserted);
    }

    public function test_on_method_sets_current_page()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->on($page = new BrowserTestPage);

        $this->assertEquals(['@modal' => '#modal'], $browser->resolver->elements);
        $this->assertEquals($page, $browser->page);
        $this->assertTrue($page->asserted);
    }

    public function test_refresh_method()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->refresh')->once();
        $browser = new Browser($driver);

        $browser->refresh();
    }

    public function test_with_method()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->with('prefix', function ($browser) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
        });
    }

    public function test_with_method_with_page()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);

        $browser->with('prefix', function ($browser) use ($page) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
            $this->assertEquals($page, $browser->page);
        });
    }

    public function test_within_method()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within('prefix', function ($browser) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
        });
    }

    public function test_within_method_with_page()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);

        $browser->within('prefix', function ($browser) use ($page) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
            $this->assertEquals($page, $browser->page);
        });
    }

    public function test_page_macros()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);
        $browser->doSomething();

        $this->assertTrue($browser->page->macroed);
    }

    public function test_retrieve_console()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('manage->getLog')->with('browser')->andReturnNull();
        $driver->shouldReceive('getCapabilities->getBrowserName')->andReturn(WebDriverBrowserType::CHROME);
        $browser = new Browser($driver);
        Browser::$storeConsoleLogAt = 'not-null';

        $browser->storeConsoleLog('file');
    }

    public function test_disable_console()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldNotReceive('manage');
        $driver->shouldReceive('getCapabilities->getBrowserName')->andReturnNull();
        $browser = new Browser($driver);

        $browser->storeConsoleLog('file');
    }
}

class BrowserTestPage extends Page
{
    public $asserted = false;
    public $macroed = false;

    public function assert(Browser $browser)
    {
        $this->asserted = true;
    }

    public function url()
    {
        return '/login';
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public static function siteElements()
    {
        return ['@modal' => '#modal'];
    }
}
